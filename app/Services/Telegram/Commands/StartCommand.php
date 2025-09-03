<?php

namespace App\Services\Telegram\Commands;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;

class StartCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'start';
    }

    public function getDescription(): string
    {
        return 'Начать работу с ботом';
    }

    public function process(TelegramMessageDto $message): void
    {
        $text = "👋 Добро пожаловать!\n\n" .
            "Я бот для управления вашим аккаунтом. " .
            "Используйте команду /about чтобы узнать больше о моих возможностях.";

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
