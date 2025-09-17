<?php

namespace App\Services\Telegram\Commands\Admin;

use App\DTOs\TelegramMessageDto;
use App\Enums\UserRole;
use App\Models\User;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;

class AdminUsersCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'users';
    }

    public function getDescription(): string
    {
        return 'Показать список пользователей';
    }

    public function canProcess(TelegramMessageDto $message): bool
    {
        if (!parent::canProcess($message)) {
            return false;
        }

        // Проверяем, что пользователь является администратором
        $user = User::where('telegram_id', $message->userId)->first();
        return $user && $user->role === UserRole::ADMIN;
    }

    public function process(TelegramMessageDto $message): void
    {
        $users = User::all();
        
        $text = "<b>Список пользователей:</b>\n\n";
        foreach ($users as $user) {
            $text .= "ID: {$user->id}\n";
            $text .= "Email: {$user->email}\n";
            $text .= "Роль: {$user->role->value}\n\n";
        }

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
