<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $resetUrl,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recuperação de Senha — Jogo de Búzios')
            ->greeting("Olá, {$notifiable->name}!")
            ->line('Recebemos uma solicitação para redefinir sua senha.')
            ->action('Redefinir Senha', $this->resetUrl)
            ->line('Este link expira em 60 minutos.')
            ->line('Se você não solicitou isso, ignore este e-mail.');
    }
}
