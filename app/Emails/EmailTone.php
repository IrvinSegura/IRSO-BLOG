<?php

namespace App\Emails;

/**
 * The mood of an email, which sets the colors of its icon and notices.
 */
enum EmailTone
{
    case Success;
    case Info;
    case Warning;

    public function accent(): string
    {
        return match ($this) {
            self::Success => '#15803d',
            self::Info => '#4f46e5',
            self::Warning => '#b45309',
        };
    }

    public function soft(): string
    {
        return match ($this) {
            self::Success => '#dcfce7',
            self::Info => '#e0e7ff',
            self::Warning => '#fef3c7',
        };
    }
}
