<?php

namespace App\Services\Telegram\Commands\Main;

use App\DTOs\TelegramMessageDto;
use App\Models\User;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;

class ProfileCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'profile';
    }

    public function getDescription(): string
    {
        return 'Показать профиль пользователя';
    }

    public function process(TelegramMessageDto $message): void
    {
        // Используем requireUser для автоматической проверки авторизации
        $user = $this->requireUser($message);
        if (!$user) {
            return; // Сообщение об авторизации уже отправлено
        }

        // Пользователь найден - показываем профиль
        $text = "👤 <b>Ваш профиль</b>\n\n" .
            "<b>Имя:</b> " . ($user->name ?? 'Не указано') . "\n" .
            "<b>Email:</b> " . ($user->email ?? 'Не указан') . "\n" .
            "<b>Роль:</b> " . ucfirst($user->role?->value ?? 'user') . "\n" .
            "<b>Дата регистрации:</b> " . $user->created_at->format('d.m.Y H:i') . "\n" .
            "<b>Telegram ID:</b> " . $user->getTelegramIdForBot($message->botId);

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
