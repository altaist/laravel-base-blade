<?php

namespace App\Services\Telegram\Base;

use App\DTOs\TelegramKeyboardDto;
use App\DTOs\TelegramMessageDto;
use App\Contracts\TelegramBotCommandInterface;
use App\Services\Telegram\TelegramService;

abstract class BaseTelegramCommand implements TelegramBotCommandInterface
{
    public function __construct(
        protected readonly TelegramService $telegram
    ) {
    }

    /**
     * Проверить, может ли команда быть выполнена для данного сообщения
     */
    public function canProcess(TelegramMessageDto $message): bool
    {
        // Проверяем, что это команда и она соответствует текущей
        // Также проверяем callback_query с командой
        return $message->command === $this->getName();
    }

    /**
     * Отправить ответ пользователю
     */
    protected function reply(TelegramMessageDto $message, string $text, ?string $parseMode = TelegramService::FORMAT_NONE): void
    {
        $this->telegram->sendMessageToUser($message->userId, $text, $parseMode);
        $this->answerCallbackQuery($message);
    }

    /**
     * Отправить ответ с клавиатурой
     */
    protected function replyWithKeyboard(
        TelegramMessageDto $message,
        string $text,
        array $buttons,
        ?string $parseMode = TelegramService::FORMAT_NONE,
        bool $oneTime = false,
        bool $resize = true
    ): void {
        $this->telegram->sendMessageWithKeyboard(
            $message->userId,
            $text,
            $buttons,
            $parseMode,
            $oneTime,
            $resize
        );
        $this->answerCallbackQuery($message);
    }

    /**
     * Отправить ответ с inline клавиатурой
     */
    protected function replyWithInlineKeyboard(
        TelegramMessageDto $message,
        string $text,
        array $buttons,
        ?string $parseMode = TelegramService::FORMAT_NONE
    ): void {
        $keyboard = new TelegramKeyboardDto($buttons, inline: true);
        $this->telegram->sendMessageWithInlineKeyboard(
            $message->userId,
            $text,
            $keyboard,
            $parseMode
        );
        $this->answerCallbackQuery($message);
    }

    /**
     * Ответить на callback_query (убрать "часики" с кнопки)
     */
    protected function answerCallbackQuery(
        TelegramMessageDto $message,
        ?string $text = null,
        bool $showAlert = false
    ): void {
        if ($message->messageType->value !== 'callback_query') {
            return;
        }

        $additionalData = json_decode($message->additionalData ?? '{}', true);
        $callbackQueryId = $additionalData['callback_query']['id'] ?? null;

        if ($callbackQueryId) {
            $this->telegram->answerCallbackQuery(
                $callbackQueryId,
                $text,
                $showAlert,
                null,
                0,
                $message->botId
            );
        }
    }
}
