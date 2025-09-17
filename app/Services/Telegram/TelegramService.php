<?php

namespace App\Services\Telegram;

use App\DTOs\TelegramKeyboardDto;
use App\DTOs\TelegramMessageDto;
use App\Enums\TelegramMessageType;
use App\Events\TelegramMessageReceived;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private TelegramBotManager $botManager;

    // Поддерживаемые форматы текста
    public const FORMAT_HTML = 'HTML';
    public const FORMAT_MARKDOWN = 'MarkdownV2';
    public const FORMAT_NONE = null;

    public function __construct(TelegramBotManager $botManager)
    {
        $this->botManager = $botManager;
    }

    /**
     * Отправить простое текстовое сообщение пользователю через основного бота
     */
    public function sendMessageToUser(
        string|int $userId,
        string $text,
        ?string $parseMode = self::FORMAT_NONE
    ): bool {
        try {
            $bot = $this->botManager->getBot('main');
            return $bot->sendMessage($userId, $text, $parseMode);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('Failed to send Telegram message', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Отправить сообщение через админского бота
     */
    public function sendAdminMessage(
        string $text,
        ?string $parseMode = self::FORMAT_NONE,
        ?string $chatId = null
    ): bool {
        try {
            $bot = $this->botManager->getBot('admin');
            
            if ($chatId) {
                return $bot->sendMessage($chatId, $text, $parseMode);
            }
            
            // Если chatId не указан, используем chatId из конфига бота
            $botChatId = $bot->getChatId();
            if (!$botChatId) {
                Log::channel('telegram')->error('Admin chat ID not configured', [
                    'bot_name' => 'admin',
                ]);
                return false;
            }
            
            return $bot->sendMessage($botChatId, $text, $parseMode);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('Failed to send admin Telegram message', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Отправить сообщение с клавиатурой через основного бота
     */
    public function sendMessageWithKeyboard(
        string|int $userId,
        string $text,
        array $buttons,
        ?string $parseMode = self::FORMAT_NONE,
        bool $oneTime = false,
        bool $resize = true
    ): bool {
        try {
            $bot = $this->botManager->getBot('main');
            return $bot->sendMessageWithKeyboard($userId, $text, $buttons, $parseMode, $oneTime, $resize);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('Failed to send Telegram message with keyboard', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Отправить сообщение с inline клавиатурой через основного бота
     */
    public function sendMessageWithInlineKeyboard(
        string|int $userId,
        string $text,
        TelegramKeyboardDto $keyboard,
        ?string $parseMode = self::FORMAT_NONE
    ): bool {
        try {
            $bot = $this->botManager->getBot('main');
            return $bot->sendMessageWithInlineKeyboard($userId, $text, $keyboard, $parseMode);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('Failed to send Telegram message with inline keyboard', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Ответить на callback_query через основного бота
     */
    public function answerCallbackQuery(
        string $callbackQueryId,
        ?string $text = null,
        bool $showAlert = false,
        ?string $url = null,
        int $cacheTime = 0,
        string $botId = 'main'
    ): bool {
        try {
            $bot = $this->botManager->getBot($botId);
            return $bot->answerCallbackQuery($callbackQueryId, $text, $showAlert, $url, $cacheTime);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('Failed to answer callback query', [
                'callback_query_id' => $callbackQueryId,
                'bot_id' => $botId,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Обработать входящее сообщение от Telegram
     */
    public function handleIncomingMessage(array $rawMessage, string $botId): void
    {
        try {
            $messageType = $this->determineMessageType($rawMessage);
            $text = '';
            $userId = null;

            if ($messageType === TelegramMessageType::CALLBACK_QUERY) {
                $text = $rawMessage['callback_query']['data'] ?? '';
                $userId = $rawMessage['callback_query']['from']['id'] ?? null;
            } else {
                $text = $rawMessage['message']['text'] ?? '';
                $userId = $rawMessage['message']['from']['id'] ?? null;
            }

            $message = new TelegramMessageDto(
                messageType: $messageType,
                text: $text,
                userId: $userId,
                additionalData: json_encode($rawMessage),
                botId: $botId,
            );

            TelegramMessageReceived::dispatch($message);

            Log::channel('telegram')->info('Telegram message received', [
                'type' => $messageType->value,
                'user_id' => $userId,
                'text' => $text,
                'command' => $message->command,
                'arguments' => $message->arguments,
                'bot_id' => $botId,
            ]);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('Failed to handle incoming Telegram message', [
                'bot_id' => $botId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Определить тип сообщения
     */
    private function determineMessageType(array $rawMessage): TelegramMessageType
    {
        if (isset($rawMessage['callback_query'])) {
            return TelegramMessageType::CALLBACK_QUERY;
        }

        if (isset($rawMessage['message']['photo'])) {
            return TelegramMessageType::PHOTO;
        }

        if (isset($rawMessage['message']['document'])) {
            return TelegramMessageType::DOCUMENT;
        }

        $text = $rawMessage['message']['text'] ?? '';

        if (str_starts_with($text, '/')) {
            return TelegramMessageType::COMMAND;
        }

        return TelegramMessageType::TEXT;
    }

    /**
     * Подготовить текст для отправки (экранирование)
     */
    private function prepareText(string $text, ?string $parseMode): string
    {
        if ($parseMode === self::FORMAT_MARKDOWN) {
            // Экранируем специальные символы для MarkdownV2
            $escaped = preg_replace('/([_*\[\]()~`>#+\-=|{}.!])/', '\\\\$1', $text);
            return $escaped ?: $text;
        }

        if ($parseMode === self::FORMAT_HTML) {
            // Для HTML не нужно экранировать теги, так как они используются для форматирования
            return $text;
        }

        return $text;
    }
}