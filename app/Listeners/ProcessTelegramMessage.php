<?php

namespace App\Listeners;

use App\Events\TelegramMessageReceived;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;

class ProcessTelegramMessage
{
    public function __construct(
        private readonly TelegramService $telegram
    ) {
    }

    public function handle(TelegramMessageReceived $event): void
    {
        // Создаем сервис для обработки команд бота
        $botService = new TelegramBotService($this->telegram, $event->message->botId);
        $botService->process($event->message);
    }
}
