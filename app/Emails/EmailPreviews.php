<?php

namespace App\Emails;

use Illuminate\Support\Carbon;

/**
 * One example of every email, with made-up data, so they can be looked at without sending anything. The tests go
 * through this list too, so a new reason has to be added here.
 */
final class EmailPreviews
{
    /**
     * @return array<string, EmailDraft>
     */
    public static function all(): array
    {
        $at = Carbon::parse('2026-11-04 18:30:00', 'UTC');

        return [
            'reset-password' => BlogEmails::resetPassword(
                'Ana López',
                url(route('password.reset', ['token' => 'token-de-ejemplo', 'email' => 'ana@example.com'], false)),
                60,
            ),
            'social-login-code' => BlogEmails::socialLoginCode('048213', 10),
            'welcome' => BlogEmails::welcome('Ana López', 'ana@example.com'),
            'password-changed' => BlogEmails::passwordChanged('Ana López', 'ana@example.com', $at),
            'comment-received' => BlogEmails::commentReceived(
                'Ana López',
                'Cómo empezar a escribir sin miedo a la página en blanco',
                'Bruno Díaz',
                'Me sirvió mucho la parte de escribir primero y corregir después. ¡Gracias por compartirlo!',
                $at,
            ),
        ];
    }
}
