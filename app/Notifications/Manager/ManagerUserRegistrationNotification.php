<?php

namespace App\Notifications\Manager;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ManagerUserRegistrationNotification extends Notification
{
    use Queueable;

    private User $newUser;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
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
        $userInfo = "📝 <b>Новая регистрация пользователя</b>\n\n";
        $userInfo .= "Имя: {$this->newUser->name}\n";
        $userInfo .= "Email: {$this->newUser->email}\n";
        $userInfo .= "Роль: {$this->newUser->role->value}\n";
        
        if ($this->newUser->telegram_username) {
            $userInfo .= "Telegram: @{$this->newUser->telegram_username}\n";
        }
        
        $userInfo .= "\nДата регистрации: " . $this->newUser->created_at->format('d.m.Y H:i');

        return [
            'text' => $userInfo,
            'parse_mode' => 'HTML',
        ];
    }
}
