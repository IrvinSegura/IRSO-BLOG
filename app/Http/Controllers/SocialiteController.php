<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SocialAccount;
use App\Models\User;
use App\Notifications\SocialLoginCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\FacebookProvider;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;
use Throwable;

class SocialiteController extends Controller
{
    /**
     * Display names used in messages shown to the user.
     *
     * @var array<string, string>
     */
    private const NAMES = [
        'google' => 'Google',
        'microsoft' => 'Microsoft',
        'facebook' => 'Facebook',
    ];

    /**
     * Session key holding a provider account that still needs a verified email.
     */
    private const PENDING = 'social_pending';

    /**
     * Wrong codes allowed before the user has to start over.
     */
    private const MAX_CODE_ATTEMPTS = 5;

    /**
     * Send the user to the provider's sign-in page.
     */
    public function redirect(string $provider): SymfonyRedirectResponse
    {
        if (blank(config("services.{$provider}.client_id"))) {
            return $this->failed(__('El acceso con :provider aún no está configurado.', ['provider' => self::NAMES[$provider]]));
        }

        $driver = Socialite::driver($provider);

        // Facebook remembers a declined email permission and never asks again unless told to.
        if ($driver instanceof FacebookProvider) {
            $driver->reRequest();
        }

        return $driver->redirect();
    }

    /**
     * Sign in, link or register the user the provider sent back.
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        // "access_denied" means the user cancelled; any other error is a setup problem worth logging.
        if ($request->has('error')) {
            if ($request->query('error') === 'access_denied') {
                return $this->failed(__('Se canceló el acceso con :provider.', ['provider' => self::NAMES[$provider]]));
            }

            Log::warning("{$provider} OAuth error", $request->only('error', 'error_description', 'error_reason'));

            return $this->failed(__('No pudimos validar tu cuenta de :provider. Inténtalo de nuevo.', ['provider' => self::NAMES[$provider]]));
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            report($e);

            return $this->failed(__('No pudimos validar tu cuenta de :provider. Inténtalo de nuevo.', ['provider' => self::NAMES[$provider]]));
        }

        $account = SocialAccount::with('user')
            ->where('provider', $provider)
            ->where('provider_id', $socialUser->getId())
            ->first();

        if ($account) {
            $account->update(['avatar' => $socialUser->getAvatar()]);

            return $this->login($request, $account->user);
        }

        $profile = [
            'provider' => $provider,
            'provider_id' => (string) $socialUser->getId(),
            'name' => $socialUser->getName(),
            'avatar' => $socialUser->getAvatar(),
        ];

        // Some accounts (e.g. Facebook accounts created with a phone number) share no email,
        // so the user types one and proves it is theirs with a code.
        if (blank($socialUser->getEmail())) {
            $request->session()->put(self::PENDING, $profile);

            return redirect()->route('social.email');
        }

        return $this->linkAndLogin($request, $profile, $socialUser->getEmail(), $this->emailIsVerified($provider, $socialUser));
    }

    /**
     * Ask for the email the provider did not share.
     */
    public function emailForm(Request $request): View|RedirectResponse
    {
        if (! $pending = $request->session()->get(self::PENDING)) {
            return redirect()->route('login');
        }

        return view('auth.social-email', [
            'provider' => self::NAMES[$pending['provider']],
            'email' => $pending['email'] ?? $pending['suggested_email'] ?? null,
            'existingAccount' => isset($pending['suggested_email']),
        ]);
    }

    /**
     * Email a one-time code to the address the user typed.
     */
    public function sendCode(Request $request): RedirectResponse
    {
        if (! $request->session()->has(self::PENDING)) {
            return redirect()->route('login');
        }

        $email = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ])['email'];

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put(self::PENDING.'.email', $email);
        $request->session()->put(self::PENDING.'.code', Hash::make($code));
        $request->session()->put(self::PENDING.'.expires_at', now()->addMinutes(10)->getTimestamp());
        $request->session()->put(self::PENDING.'.attempts', 0);

        Notification::route('mail', $email)->notify(new SocialLoginCode($code));

        return redirect()->route('social.verify')
            ->with('status', __('Te enviamos un código a :email.', ['email' => $email]));
    }

    /**
     * Ask for the code sent by email.
     */
    public function codeForm(Request $request): View|RedirectResponse
    {
        $pending = $request->session()->get(self::PENDING);

        if (! isset($pending['email'])) {
            return redirect()->route('social.email');
        }

        return view('auth.social-verify', ['email' => $pending['email']]);
    }

    /**
     * Check the code and finish signing in with the now verified email.
     */
    public function verifyCode(Request $request): RedirectResponse
    {
        $pending = $request->session()->get(self::PENDING);

        if (! isset($pending['email'])) {
            return redirect()->route('social.email');
        }

        $request->validate(['code' => ['required', 'string']]);

        if (now()->getTimestamp() > $pending['expires_at']) {
            return redirect()->route('social.email')
                ->withErrors(['code' => __('El código venció. Pide uno nuevo.')]);
        }

        if (! Hash::check($request->input('code'), $pending['code'])) {
            $attempts = $pending['attempts'] + 1;

            if ($attempts >= self::MAX_CODE_ATTEMPTS) {
                $request->session()->forget(self::PENDING);

                return $this->failed(__('Demasiados intentos con un código incorrecto. Vuelve a empezar.'));
            }

            $request->session()->put(self::PENDING.'.attempts', $attempts);

            return back()->withErrors(['code' => __('El código no es correcto.')]);
        }

        $request->session()->forget(self::PENDING);

        return $this->linkAndLogin($request, $pending, $pending['email'], emailVerified: true);
    }

    /**
     * Link the provider account to the user with this email, or to a new user, and sign in.
     *
     * @param  array{provider: string, provider_id: string, name: ?string, avatar: ?string}  $profile
     */
    private function linkAndLogin(Request $request, array $profile, string $email, bool $emailVerified): RedirectResponse
    {
        $user = User::withTrashed()->where('email', $email)->first();

        if ($user?->trashed()) {
            return $this->failed(__('Tu cuenta está deshabilitada. Contacta a un administrador.'));
        }

        // Linking by email is only safe when the address is verified, otherwise anyone
        // could claim an existing account, so the user proves it with a code first.
        if ($user && ! $emailVerified) {
            $request->session()->put(self::PENDING, $profile + ['suggested_email' => $email]);

            return redirect()->route('social.email');
        }

        $user = DB::transaction(function () use ($user, $profile, $email, $emailVerified) {
            $user ??= User::forceCreate([
                'name' => filled($profile['name']) ? trim($profile['name']) : Str::before($email, '@'),
                'email' => $email,
                'email_verified_at' => $emailVerified ? now() : null,
                // Users can set a real password later with "forgot password".
                'password' => Hash::make(Str::random(40)),
                'rol' => Role::USER,
            ]);

            $user->socialAccounts()->create([
                'provider' => $profile['provider'],
                'provider_id' => $profile['provider_id'],
                'avatar' => $profile['avatar'],
            ]);

            return $user;
        });

        return $this->login($request, $user);
    }

    /**
     * Start the session, or the two-factor challenge when the user enabled it.
     */
    private function login(Request $request, User $user): RedirectResponse
    {
        if ($user->trashed()) {
            return $this->failed(__('Tu cuenta está deshabilitada. Contacta a un administrador.'));
        }

        // Same session keys Fortify reads in its two-factor challenge.
        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            $request->session()->put([
                'login.id' => $user->getKey(),
                'login.remember' => false,
            ]);

            return redirect()->route('two-factor.login');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(config('fortify.home'));
    }

    /**
     * Whether the provider guarantees the user owns the email address.
     */
    private function emailIsVerified(string $provider, SocialiteUser $socialUser): bool
    {
        return match ($provider) {
            'google' => ($socialUser->getRaw()['email_verified'] ?? false) === true,
            // Facebook only shares confirmed email addresses.
            'facebook' => true,
            // Microsoft returns the account's principal name, which is not verified.
            default => false,
        };
    }

    /**
     * Go back to the login page with an error message.
     */
    private function failed(string $message): RedirectResponse
    {
        return redirect()->route('login')->withErrors(['social' => $message]);
    }
}
