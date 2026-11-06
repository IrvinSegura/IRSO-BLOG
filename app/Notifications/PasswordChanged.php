<?php

namespace App\Notifications;

use App\Emails\BlogEmails;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChanged extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return BlogEmails::passwordChanged($notifiable->name, $notifiable->email, now())->toMail();
    }
}
