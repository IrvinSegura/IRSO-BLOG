<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Notifications\SocialLoginCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class SocialiteAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_are_redirected_to_the_provider(): void
    {
        config(['services.google.client_id' => 'test-client-id', 'services.google.client_secret' => 'test-secret']);

        $response = $this->get('/auth/google/redirect');

        $this->assertStringStartsWith('https://accounts.google.com/', $response->headers->get('Location'));
    }

    public function test_facebook_asks_again_for_declined_permissions(): void
    {
        config(['services.facebook.client_id' => 'test-client-id', 'services.facebook.client_secret' => 'test-secret']);

        $response = $this->get('/auth/facebook/redirect');

        $this->assertStringContainsString('auth_type=rerequest', $response->headers->get('Location'));
    }

    public function test_redirect_fails_when_the_provider_is_not_configured(): void
    {
        config(['services.facebook.client_id' => null]);

        $this->get('/auth/facebook/redirect')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('social');
    }

    public function test_unknown_providers_are_not_found(): void
    {
        $this->get('/auth/github/redirect')->assertNotFound();
    }

    public function test_new_users_can_register_with_a_provider(): void
    {
        $this->fakeProviderUser('google', '123', 'nuevo@gmail.com', ['email_verified' => true]);

        $response = $this->get('/auth/google/callback');

        $user = User::where('email', 'nuevo@gmail.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('blog.index', absolute: false));
        $this->assertSame(Role::USER, (int) $user->rol);
        $this->assertNotNull($user->email_verified_at);
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id, 'provider' => 'google', 'provider_id' => '123']);
    }

    public function test_blank_provider_names_fall_back_to_the_email(): void
    {
        $this->fakeProviderUser('microsoft', 'abc', 'sin.nombre@outlook.com', name: ' ');

        $this->get('/auth/microsoft/callback');

        $this->assertSame('sin.nombre', User::where('email', 'sin.nombre@outlook.com')->value('name'));
    }

    public function test_existing_users_are_linked_when_the_provider_verified_the_email(): void
    {
        $user = User::factory()->create(['email' => 'existente@gmail.com']);
        $this->fakeProviderUser('google', '123', 'existente@gmail.com', ['email_verified' => true]);

        $this->get('/auth/google/callback');

        $this->assertAuthenticatedAs($user);
        $this->assertSame(1, User::count());
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id, 'provider' => 'google']);
    }

    public function test_unverified_provider_emails_must_be_confirmed_before_linking(): void
    {
        User::factory()->create(['email' => 'victima@outlook.com']);
        $this->fakeProviderUser('microsoft', 'abc', 'victima@outlook.com');

        $this->get('/auth/microsoft/callback')->assertRedirect(route('social.email'));

        $this->assertGuest();
        $this->assertDatabaseCount('social_accounts', 0);
        $this->get(route('social.email'))->assertSee('victima@outlook.com', false);
    }

    public function test_existing_users_are_linked_after_confirming_the_email_by_code(): void
    {
        $user = User::factory()->create(['email' => 'mio@outlook.com']);
        $this->fakeProviderUser('microsoft', 'abc', 'mio@outlook.com');

        $this->get('/auth/microsoft/callback');
        $code = $this->requestCode('mio@outlook.com');

        $this->post(route('social.verify.check'), ['code' => $code])
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id, 'provider' => 'microsoft', 'provider_id' => 'abc']);
    }

    public function test_accounts_without_email_register_after_confirming_one_by_code(): void
    {
        $this->fakeProviderUser('facebook', 'fb-1', '');

        $this->get('/auth/facebook/callback')->assertRedirect(route('social.email'));
        $code = $this->requestCode('nuevo@gmail.com');

        $this->get(route('social.verify'))->assertOk()->assertSee('nuevo@gmail.com');
        $this->post(route('social.verify.check'), ['code' => $code])
            ->assertRedirect(route('blog.index', absolute: false));

        $user = User::where('email', 'nuevo@gmail.com')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->email_verified_at);
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id, 'provider' => 'facebook', 'provider_id' => 'fb-1']);
    }

    public function test_wrong_codes_are_rejected_and_limited(): void
    {
        $this->fakeProviderUser('facebook', 'fb-1', '');
        $this->get('/auth/facebook/callback');
        $this->requestCode('nuevo@gmail.com');

        foreach (range(1, 4) as $attempt) {
            $this->post(route('social.verify.check'), ['code' => 'xxxxxx'])->assertSessionHasErrors('code');
        }

        $this->post(route('social.verify.check'), ['code' => 'xxxxxx'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('social');

        $this->assertGuest();
        $this->get(route('social.email'))->assertRedirect(route('login'));
    }

    public function test_expired_codes_are_rejected(): void
    {
        $this->fakeProviderUser('facebook', 'fb-1', '');
        $this->get('/auth/facebook/callback');
        $code = $this->requestCode('nuevo@gmail.com');

        $this->travel(11)->minutes();

        $this->post(route('social.verify.check'), ['code' => $code])
            ->assertRedirect(route('social.email'))
            ->assertSessionHasErrors('code');

        $this->assertGuest();
    }

    public function test_the_email_step_requires_a_pending_provider_account(): void
    {
        $this->get(route('social.email'))->assertRedirect(route('login'));
        $this->post(route('social.email.send'), ['email' => 'nuevo@gmail.com'])->assertRedirect(route('login'));
    }

    public function test_linked_users_sign_in_by_provider_id(): void
    {
        $user = User::factory()->create();
        $user->socialAccounts()->create(['provider' => 'facebook', 'provider_id' => 'fb-1']);
        $this->fakeProviderUser('facebook', 'fb-1', 'otro-correo@example.com');

        $this->get('/auth/facebook/callback');

        $this->assertAuthenticatedAs($user);
    }

    public function test_hidden_users_can_not_sign_in_with_a_provider(): void
    {
        $user = User::factory()->create(['email' => 'oculto@gmail.com']);
        $user->delete();
        $this->fakeProviderUser('google', '123', 'oculto@gmail.com', ['email_verified' => true]);

        $this->get('/auth/google/callback')->assertSessionHasErrors('social');

        $this->assertGuest();
        $this->assertDatabaseCount('social_accounts', 0);
    }

    public function test_cancelled_sign_ins_return_to_the_login_page(): void
    {
        $this->get('/auth/google/callback?error=access_denied')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('social');

        $this->assertGuest();
    }

    public function test_provider_errors_are_logged(): void
    {
        Log::shouldReceive('warning')->once()->with('microsoft OAuth error', [
            'error' => 'invalid_request',
            'error_description' => 'AADSTS50011: redirect mismatch',
        ]);

        $this->get('/auth/microsoft/callback?error=invalid_request&error_description=AADSTS50011:+redirect+mismatch')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('social');

        $this->assertGuest();
    }

    public function test_users_with_two_factor_authentication_must_complete_the_challenge(): void
    {
        $user = User::factory()->create([
            'two_factor_secret' => encrypt('secret'),
            'two_factor_confirmed_at' => now(),
        ]);
        $user->socialAccounts()->create(['provider' => 'google', 'provider_id' => '123']);
        $this->fakeProviderUser('google', '123', $user->email, ['email_verified' => true]);

        $this->get('/auth/google/callback')
            ->assertRedirect(route('two-factor.login'))
            ->assertSessionHas('login.id', $user->id);

        $this->assertGuest();
    }

    /**
     * Submit the email step and return the code that was emailed.
     */
    private function requestCode(string $email): string
    {
        Notification::fake();

        $this->post(route('social.email.send'), ['email' => $email])->assertRedirect(route('social.verify'));

        $code = null;

        Notification::assertSentOnDemand(SocialLoginCode::class, function (SocialLoginCode $notification, array $channels, object $notifiable) use ($email, &$code) {
            $code = $notification->code;

            return $notifiable->routes['mail'] === $email;
        });

        return $code;
    }

    /**
     * Make Socialite return the given user for the provider callback.
     *
     * @param  array<string, mixed>  $raw
     */
    private function fakeProviderUser(string $provider, string $id, string $email, array $raw = [], string $name = 'Usuario Social'): void
    {
        $socialUser = (new SocialiteUser)->setRaw($raw)->map([
            'id' => $id,
            'name' => $name,
            'email' => $email,
            'avatar' => 'https://example.com/avatar.png',
        ]);

        Socialite::shouldReceive('driver')->with($provider)->andReturnSelf();
        Socialite::shouldReceive('user')->andReturn($socialUser);
    }
}
