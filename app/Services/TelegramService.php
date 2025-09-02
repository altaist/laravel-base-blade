<?php

namespace App\Services;

use App\DTOs\TelegramKeyboardDto;
use App\DTOs\TelegramMessageDto;
use App\Enums\TelegramMessageType;
use App\Events\TelegramMessageReceived;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    private PendingRequest $http;
    private string $baseUrl;

    // Поддерживаемые форматы текста
    public const FORMAT_HTML = 'HTML';
    public const FORMAT_MARKDOWN = 'MarkdownV2';
    public const FORMAT_NONE = null;

    public function __construct()
    {
        $token = config('services.telegram.bot.token');
        $this->baseUrl = "https://api.telegram.org/bot{$token}";
        $this->http = Http::baseUrl($this->baseUrl)->throw();
    }

    /**
     * Отправить простое текстовое сообщение пользователю
     */
    public function sendMessageToUser(
        string|int $userId,
        string $text,
        ?string $parseMode = self::FORMAT_NONE
    ): bool {
        try {
            $params = [
                'chat_id' => $userId,
                'text' => $this->prepareText($text, $parseMode),
            ];

            if ($parseMode) {
                $params['parse_mode'] = $parseMode;
            }

            $response = $this->http->post('/sendMessage', $params);

            Log::info('Telegram message sent', [
                'user_id' => $userId,
                'text' => $text,
                'parse_mode' => $parseMode,
            ]);

            return true;
        } catch (RequestException $e) {
            Log::error('Failed to send Telegram message', [
                'user_id' => $userId,
                'text' => $text,
                'parse_mode' => $parseMode,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Отправить сообщение с inline клавиатурой
     */
    public function sendMessageWithInlineKeyboard(
        string|int $userId,
        string $text,
        TelegramKeyboardDto $keyboard,
        ?string $parseMode = self::FORMAT_NONE
    ): bool {
        try {
            $params = [
                'chat_id' => $userId,
                'text' => $this->prepareText($text, $parseMode),
                'reply_markup' => json_encode($keyboard->toArray()),
            ];

            if ($parseMode) {
                $params['parse_mode'] = $parseMode;
            }

            $response = $this->http->post('/sendMessage', $params);

            Log::info('Telegram message with keyboard sent', [
                'user_id' => $userId,
                'text' => $text,
                'keyboard' => $keyboard->toArray(),
                'parse_mode' => $parseMode,
            ]);

            return true;
        } catch (RequestException $e) {
            Log::error('Failed to send Telegram message with keyboard', [
                'user_id' => $userId,
                'text' => $text,
                'keyboard' => $keyboard->toArray(),
                'parse_mode' => $parseMode,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Отправить сообщение с обычной клавиатурой
     */
    public function sendMessageWithKeyboard(
        string|int $userId,
        string $text,
        array $buttons,
        ?string $parseMode = self::FORMAT_NONE,
        bool $oneTime = false,
        bool $resize = true
    ): bool {
        $keyboard = new TelegramKeyboardDto($buttons, inline: false, oneTime: $oneTime, resize: $resize);
        
        try {
            $params = [
                'chat_id' => $userId,
                'text' => $this->prepareText($text, $parseMode),
                'reply_markup' => json_encode($keyboard->toArray()),
            ];

            if ($parseMode) {
                $params['parse_mode'] = $parseMode;
            }

            $response = $this->http->post('/sendMessage', $params);

            Log::info('Telegram message with reply keyboard sent', [
                'user_id' => $userId,
                'text' => $text,
                'keyboard' => $keyboard->toArray(),
                'parse_mode' => $parseMode,
            ]);

            return true;
        } catch (RequestException $e) {
            Log::error('Failed to send Telegram message with reply keyboard', [
                'user_id' => $userId,
                'text' => $text,
                'keyboard' => $keyboard->toArray(),
                'parse_mode' => $parseMode,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendMessageToBot(string $text, ?string $parseMode = self::FORMAT_NONE): bool
    {
        $botName = config('services.telegram.bot.name');
        return $this->sendMessageToUser($botName, $text, $parseMode);
    }

    public function handleIncomingMessage(array $rawMessage): void
    {
        try {
            $messageType = $this->determineMessageType($rawMessage);
            $userId = null;
            $text = '';

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
            );

            TelegramMessageReceived::dispatch($message);

            Log::info('Telegram message received', [
                'type' => $messageType->value,
                'user_id' => $userId,
                'text' => $text,
                'command' => $message->command,
                'arguments' => $message->arguments,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to handle incoming Telegram message', [
                'raw_message' => $rawMessage,
                'error' => $e->getMessage(),
            ]);
        }
    }

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
     * Подготовить текст для отправки с учетом формата
     */
    private function prepareText(string $text, ?string $parseMode): string
    {
        if ($parseMode === self::FORMAT_MARKDOWN) {
            // Экранируем специальные символы Markdown
            $specialChars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
            return str_replace($specialChars, array_map(fn($char) => "\\$char", $specialChars), $text);
        }

        if ($parseMode === self::FORMAT_HTML) {
            // Для HTML не нужно экранировать теги, так как они используются для форматирования
            return $text;
        }

        return $text;
    }
}