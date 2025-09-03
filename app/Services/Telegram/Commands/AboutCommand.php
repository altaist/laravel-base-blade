<?php

namespace App\Services\Telegram\Commands;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;

class AboutCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'about';
    }

    public function getDescription(): string
    {
        return 'Информация о боте и доступных командах';
    }

    public function process(TelegramMessageDto $message): void
    {
        $text = "ℹ️ <b>О боте</b>\n\n" .
            "Я помогаю управлять вашим аккаунтом и предоставляю доступ к различным функциям.\n\n" .
            "<b>Доступные команды:</b>\n" .
            "/start - Начать работу с ботом\n" .
            "/about - Показать это сообщение\n";

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
