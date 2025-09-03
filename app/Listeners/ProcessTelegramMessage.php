<?php

namespace App\Listeners;

use App\Events\TelegramMessageReceived;
use App\Services\Telegram\TelegramBotService;

class ProcessTelegramMessage
{
    public function __construct(
        private readonly TelegramBotService $botService
    ) {
    }

    public function handle(TelegramMessageReceived $event): void
    {
        $this->botService->process($event->message);
    }
}
