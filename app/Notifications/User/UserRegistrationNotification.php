<?php

namespace App\Notifications\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserRegistrationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
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
        return ['telegram'];
    }

    /**
     * Get the Telegram representation of the notification.
     */
    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => "🎉 Добро пожаловать, {$notifiable->name}!\n\n" .
                     "Вы успешно зарегистрированы в системе.\n" .
                     "Теперь вы можете пользоваться всеми возможностями платформы.",
            'parse_mode' => 'HTML',
        ];
    }
}
