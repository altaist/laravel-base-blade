<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminUserRegistrationNotification extends Notification
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
        $userInfo = "👤 <b>Новый пользователь зарегистрирован</b>\n\n";
        $userInfo .= "ID: {$this->newUser->id}\n";
        $userInfo .= "Имя: {$this->newUser->name}\n";
        $userInfo .= "Email: {$this->newUser->email}\n";
        $userInfo .= "Роль: {$this->newUser->role->value}\n";
        
        $telegramId = $this->newUser->getTelegramIdForBot('main');
        if ($telegramId) {
            $userInfo .= "Telegram ID: {$telegramId}\n";
        }

        $telegramUsername = $this->newUser->getTelegramUsernameForBot('main');
        if ($telegramUsername) {
            $userInfo .= "Telegram: @{$telegramUsername}\n";
        }
        
        $userInfo .= "\nДата регистрации: " . $this->newUser->created_at->format('d.m.Y H:i');

        return [
            'text' => $userInfo,
            'parse_mode' => 'HTML',
        ];
    }
}
