<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SocialLoginCode extends Notification
{
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
        return (new MailMessage)
            ->subject(__('Tu código de verificación: :code', ['code' => $this->code]))
            ->greeting(__('¡Hola!'))
            ->line(__('Usa este código para confirmar tu correo y terminar de entrar a :app:', ['app' => config('app.name')]))
            ->line(new HtmlString('<strong style="font-size: 28px; letter-spacing: 6px;">'.$this->code.'</strong>'))
            ->line(__('El código vence en 10 minutos. Si no intentaste entrar, ignora este correo.'))
            ->salutation(__('Saludos, :app', ['app' => config('app.name')]));
    }
}
