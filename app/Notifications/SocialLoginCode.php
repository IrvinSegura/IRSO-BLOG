<?php

namespace App\Notifications;

use App\Emails\BlogEmails;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SocialLoginCode extends Notification
{
    /**
     * How long the code works, in minutes.
     */
    public const EXPIRES_IN_MINUTES = 10;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly string $code)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return BlogEmails::socialLoginCode($this->code, self::EXPIRES_IN_MINUTES)->toMail();
    }
}
