<?php

namespace App\Providers;

use App\Emails\BlogEmails;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Microsoft\MicrosoftExtendSocialite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Microsoft is not a built-in Socialite driver, so it is registered here.
        Event::listen(SocialiteWasCalled::class, [MicrosoftExtendSocialite::class, 'handle']);

        // Fortify sends Laravel's own notification; only its design is replaced.
        ResetPassword::toMailUsing(fn (object $notifiable, string $token) => BlogEmails::resetPassword(
            $notifiable->name,
            url(route('password.reset', ['token' => $token, 'email' => $notifiable->getEmailForPasswordReset()], false)),
            (int) config('auth.passwords.'.config('auth.defaults.passwords').'.expire'),
        )->toMail());
    }
}
