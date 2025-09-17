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
        try {
            // Получаем синглтон сервиса для обработки команд бота
            $botService = app("telegram.{$event->message->botId}");
            $botService->process($event->message);
        } catch (\Exception $e) {
            // Логируем ошибку, если бот не найден
            \Illuminate\Support\Facades\Log::channel('telegram')->error('Failed to process Telegram message', [
                'bot_id' => $event->message->botId,
                'user_id' => $event->message->userId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
