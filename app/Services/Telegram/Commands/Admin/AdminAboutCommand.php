<?php

namespace App\Services\Telegram\Commands\Admin;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;

class AdminAboutCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'about';
    }

    public function getDescription(): string
    {
        return 'Информация об админском боте';
    }

    public function canProcess(TelegramMessageDto $message): bool
    {
        if (!parent::canProcess($message)) {
            return false;
        }

        // Проверяем, что пользователь является администратором
        $user = \App\Models\User::where('telegram_id', $message->userId)->first();
        return $user && $user->role === \App\Enums\UserRole::ADMIN;
    }

    public function process(TelegramMessageDto $message): void
    {
        $chatId = config('telegram.bots.admin.chat_id');
        
        $text = "🔧 <b>Админский бот</b>\n\n" .
            "Я помогаю администраторам управлять системой.\n\n" .
            "<b>Ваш chat_id:</b> <code>{$chatId}</code>\n\n" .
            "<b>Доступные команды:</b>\n" .
            "/users - Показать список пользователей\n" .
            "/about - Показать это сообщение\n";

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
