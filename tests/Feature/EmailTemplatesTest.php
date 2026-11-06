<?php

namespace Tests\Feature;

use App\Emails\BlogEmails;
use App\Emails\EmailPreviews;
use App\Models\Category;
use App\Models\Publication;
use App\Models\Role;
use App\Models\User;
use App\Notifications\CommentReceived;
use App\Notifications\PasswordChanged;
use App\Notifications\SocialLoginCode;
use App\Notifications\WelcomeEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\SentMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage as SymfonySentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Tests\TestCase;

class EmailTemplatesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function reasons(): array
    {
        return [
            'reset-password' => ['reset-password'],
            'social-login-code' => ['social-login-code'],
            'welcome' => ['welcome'],
            'password-changed' => ['password-changed'],
            'comment-received' => ['comment-received'],
        ];
    }

    public function test_the_previews_cover_every_reason(): void
    {
        $this->assertEqualsCanonicalizing(array_keys(self::reasons()), array_keys(EmailPreviews::all()));
    }

    #[DataProvider('reasons')]
    public function test_every_email_has_the_same_structure_and_no_text_left_unfilled(string $reason): void
    {
        $draft = EmailPreviews::all()[$reason];
        $html = $draft->html();
        $text = $draft->text();

        foreach ([$draft->subject, $draft->preheader, $draft->title, $draft->greeting] as $value) {
            $this->assertNotSame('', trim($value));
        }

        foreach ([$html, $text, $draft->subject] as $form) {
            $this->assertStringNotContainsString('emails.', $form, 'A text is missing from lang/es/emails.php');
        }

        // Every :value of the wording was filled in
        $this->assertDoesNotMatchRegularExpression('/(^|\s|«):[a-z_]+/', $text);

        $this->assertStringContainsString('<html lang="es"', $html);
        $this->assertStringContainsString(e($draft->kicker), $html);
        $this->assertStringContainsString(e($draft->signOff), $html);
        $this->assertStringContainsString($draft->signOff, $text);
        $this->assertStringContainsString('role="presentation"', $html);
        $this->assertStringContainsString('IRSO BLOG', $html);
        $this->assertStringContainsString(e($draft->icon), $html);
        $this->assertStringContainsString(e($draft->title), $html);
        $this->assertStringContainsString(e($draft->greeting), $html);
        $this->assertStringContainsString(e($draft->preheader), $html);
        $this->assertStringContainsString('Nunca te pediremos', $html);
        $this->assertStringContainsString('Nunca te pediremos', $text);
        $this->assertStringStartsWith($draft->greeting, $text);
        $this->assertStringNotContainsString('<', strip_tags($text));
        $this->assertDoesNotMatchRegularExpression('/<(link|script|img)\b/i', $html);
    }

    public function test_the_details_appear_in_a_table_and_the_action_in_both_forms(): void
    {
        $draft = BlogEmails::resetPassword('Ana', 'https://blog.example.com/reset-password/abc?email=ana%40example.com', 60);

        $this->assertStringContainsString('60 minutos', $draft->html());
        $this->assertStringContainsString('El enlace vence en: 60 minutos', $draft->text());
        $this->assertSame(2, substr_count($draft->html(), 'href="https://blog.example.com/reset-password/abc?email=ana%40example.com"'));
        $this->assertStringContainsString('https://blog.example.com/reset-password/abc?email=ana%40example.com', $draft->text());
        $this->assertStringContainsString('Si el botón no funciona', $draft->html());
    }

    public function test_what_users_wrote_cannot_add_markup_to_the_email(): void
    {
        $attack = '<img src=x onerror=alert(1)><a href="https://evil.example">Soporte</a>';

        $html = BlogEmails::commentReceived($attack, $attack, $attack, $attack, Carbon::now())->html();

        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringNotContainsString('<a href="https://evil.example"', $html);
        $this->assertStringContainsString('&lt;img src=x', $html);
    }

    public function test_the_icons_are_typographic_marks_and_not_emoji(): void
    {
        foreach (EmailPreviews::all() as $reason => $draft) {
            $this->assertDoesNotMatchRegularExpression('/\p{Extended_Pictographic}\x{FE0F}|[\x{1F000}-\x{1FAFF}]/u', $draft->icon, $reason);
        }
    }

    public function test_the_comment_is_quoted_in_both_forms_with_its_author(): void
    {
        $draft = BlogEmails::commentReceived('Ana', 'Mi historia', 'Bruno Díaz', 'Muy bueno', Carbon::now());

        $this->assertStringContainsString('&ldquo;Muy bueno&rdquo;', $draft->html());
        $this->assertStringContainsString("Bruno Díaz:\n\"Muy bueno\"", $draft->text());
        $this->assertSame('BD', $draft->quoteInitials);
    }

    public function test_the_welcome_email_lists_its_steps_in_both_forms(): void
    {
        $draft = BlogEmails::welcome('Ana', 'ana@example.com');

        $this->assertCount(3, $draft->steps);
        $this->assertStringContainsString(e($draft->steps[0]['title']), $draft->html());
        $this->assertStringContainsString('1. '.$draft->steps[0]['title'], $draft->text());
    }

    public function test_the_greeting_only_takes_a_plain_first_name(): void
    {
        $this->assertSame('Hola Ana,', BlogEmails::greeting('Ana López'));
        $this->assertSame('Hola Gana,', BlogEmails::greeting('Gana dinero en evil.example'));
        $this->assertSame("Hola O'Brien,", BlogEmails::greeting("O'Brien"));
        $this->assertSame('¡Hola!', BlogEmails::greeting('evil.example'));
        $this->assertSame('¡Hola!', BlogEmails::greeting('https://evil.example'));
        $this->assertSame('¡Hola!', BlogEmails::greeting('<b>Ana</b>'));
        $this->assertSame('¡Hola!', BlogEmails::greeting(null));
    }

    public function test_the_email_goes_out_with_both_forms(): void
    {
        $user = User::factory()->create(['name' => 'Ana López', 'email' => 'ana@example.com']);

        $user->notify(new WelcomeEmail);

        $email = $this->sentMessages()->sole()->getOriginalMessage();

        $this->assertSame('Te damos la bienvenida a IRSO BLOG', $email->getSubject());
        $this->assertSame('ana@example.com', $email->getTo()[0]->getAddress());
        $this->assertStringContainsString('<table role="presentation"', $email->getHtmlBody());
        $this->assertStringStartsWith('Hola Ana,', $email->getTextBody());
        $this->assertStringNotContainsString('<table', $email->getTextBody());
        $this->assertStringContainsString('text/plain', $email->toString());
        $this->assertStringContainsString('text/html', $email->toString());
    }

    public function test_the_password_reset_email_is_the_designed_one_with_a_working_link(): void
    {
        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHasNoErrors();

        $email = $this->sentMessages()->sole()->getOriginalMessage();

        $this->assertSame('Restablece tu contraseña de IRSO BLOG', $email->getSubject());
        $this->assertSame(1, preg_match('#https?://\S+/reset-password/\S+#', $email->getTextBody(), $link));
        $this->assertStringContainsString('href="'.e($link[0]).'"', $email->getHtmlBody());
        $this->get($link[0])->assertOk();
    }

    public function test_the_login_code_reaches_both_forms(): void
    {
        Notification::route('mail', 'ana@example.com')->notify(new SocialLoginCode('048213'));

        $email = $this->sentMessages()->sole()->getOriginalMessage();

        $this->assertSame('Tu código de verificación: 048213', $email->getSubject());
        $this->assertStringContainsString('Tu código: 048213', $email->getTextBody());
        $this->assertStringContainsString('048213', $email->getHtmlBody());
    }

    public function test_registering_sends_the_welcome_email(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Ana López',
            'email' => 'ana@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        Notification::assertSentTo(User::where('email', 'ana@example.com')->sole(), WelcomeEmail::class);
    }

    public function test_changing_the_password_sends_the_notice(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->actingAs($user)->put('/user/password', [
            'current_password' => 'password',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        Notification::assertSentTo($user, PasswordChanged::class);
    }

    public function test_resetting_the_password_sends_the_notice(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])->assertSessionHasNoErrors();

            return true;
        });

        Notification::assertSentTo($user, PasswordChanged::class);
    }

    public function test_the_author_hears_about_a_comment_from_someone_else(): void
    {
        Notification::fake();

        [$author, $publication] = $this->publication();
        $reader = User::factory()->create(['rol' => Role::USER]);

        $this->actingAs($reader)->post(route('admin.publications.comments.store', $publication), ['comment' => 'Muy bueno'])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($author, CommentReceived::class, fn (CommentReceived $notification) => $notification->commenter->is($reader)
            && str_contains($notification->toMail($author)->viewData['draft']->quote, 'Muy bueno'));
    }

    public function test_authors_are_not_told_about_their_own_comments(): void
    {
        Notification::fake();

        [$author, $publication] = $this->publication();

        $this->actingAs($author)->post(route('admin.publications.comments.store', $publication), ['comment' => 'Gracias'])
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_a_mail_server_failure_does_not_stop_a_registration(): void
    {
        Mail::extend('broken', fn () => new class extends AbstractTransport
        {
            protected function doSend(SymfonySentMessage $message): void
            {
                throw new TransportException('Mail server is down');
            }

            public function __toString(): string
            {
                return 'broken';
            }
        });
        config(['mail.mailers.broken' => ['transport' => 'broken'], 'mail.default' => 'broken']);

        $this->post('/register', [
            'name' => 'Ana López',
            'email' => 'ana@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('blog.index'));

        $this->assertAuthenticated();
    }

    public function test_the_preview_command_writes_every_design_without_sending_anything(): void
    {
        $path = storage_path('framework/testing/email-previews-'.uniqid());

        try {
            $this->artisan('emails:preview', ['--path' => $path])->assertSuccessful();

            $this->assertFileExists("{$path}/index.html");

            foreach (array_keys(self::reasons()) as $reason) {
                $this->assertFileExists("{$path}/{$reason}.html");
                $this->assertFileExists("{$path}/{$reason}.txt");
            }

            $this->assertCount(0, $this->sentMessages());
        } finally {
            File::deleteDirectory($path);
        }
    }

    /**
     * @return Collection<int, SentMessage|SymfonySentMessage>
     */
    private function sentMessages()
    {
        return collect(app('mailer')->getSymfonyTransport()->messages());
    }

    /**
     * @return array{User, Publication}
     */
    private function publication(): array
    {
        $author = User::factory()->create(['rol' => Role::USER]);

        $publication = new Publication;
        $publication->forceFill([
            'title' => 'Mi publicación',
            'slug' => 'mi-publicacion',
            'src_img' => 'portada.png',
            'content' => 'Contenido',
            'status' => Publication::STATUS_PUBLISHED,
            'user_id' => $author->id,
            'category_id' => Category::create(['name' => 'Deportes'])->id,
        ])->save();

        return [$author, $publication];
    }
}
