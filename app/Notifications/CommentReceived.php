<?php

namespace App\Notifications;

use App\Emails\BlogEmails;
use App\Models\Comment;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReceived extends Notification
{
    public function __construct(
        public readonly Publication $publication,
        public readonly Comment $comment,
        public readonly User $commenter,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return BlogEmails::commentReceived(
            $notifiable->name,
            $this->publication->title,
            $this->commenter->name,
            $this->comment->comment,
            $this->comment->created_at,
        )->toMail();
    }
}
