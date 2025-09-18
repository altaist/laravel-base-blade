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
        // Ищем пользователя по telegram_id для этого бота
        $user = $this->findUser($message);

        if ($user) {
            // Пользователь найден - показываем профиль
            $text = "👤 <b>Ваш профиль</b>\n\n" .
                "<b>Имя:</b> " . ($user->name ?? 'Не указано') . "\n" .
                "<b>Email:</b> " . ($user->email ?? 'Не указан') . "\n" .
                "<b>Роль:</b> " . ucfirst($user->role?->value ?? 'user') . "\n" .
                "<b>Дата регистрации:</b> " . $user->created_at->format('d.m.Y H:i') . "\n" .
                "<b>Telegram ID:</b> " . $user->getTelegramIdForBot($message->botId);
        } else {
            // Пользователь не найден - предлагаем регистрацию
            $this->sendUnauthorizedMessage($message);
            return;
        }

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
