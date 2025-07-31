<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentAccountRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        #[\SensitiveParameter] private string $password,
    )
    {}

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Аккаунт ученика успешно зарегистрирован')
            ->line('Ваш аккаунт ученика успешно зарегистрирован!')
            ->line("Email:{$notifiable->email}")
            ->line("Пароль:{$this->password}")
            ->action('Вход', route('login'));
    }
}
