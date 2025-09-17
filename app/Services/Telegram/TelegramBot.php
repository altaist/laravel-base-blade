<?php

namespace App\Services\Telegram;

use App\DTOs\TelegramKeyboardDto;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBot
{
    private PendingRequest $http;
    private string $apiUrl;

    public function __construct(
        private readonly string $name,
        private readonly string $token,
        private readonly ?string $chatId = null,
        private readonly ?string $baseApiUrl = null
    ) {
        $apiUrl = $baseApiUrl ?? config('telegram.api_url', 'https://api.telegram.org');
        $this->apiUrl = "{$apiUrl}/bot{$token}";
        $this->http = Http::baseUrl($this->apiUrl)->throw();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    /**
     * Отправить простое текстовое сообщение пользователю
     */
    public function sendMessage(
        string|int $userId,
        string $text,
        ?string $parseMode = null
    ): bool {
        try {
            $params = [
                'chat_id' => $userId,
                'text' => $text,
            ];

            if ($parseMode) {
                $params['parse_mode'] = $parseMode;
            }

            $response = $this->http->post('/sendMessage', $params);

            if ($response->successful()) {
                Log::channel('telegram')->info('Telegram message sent', [
                    'bot_name' => $this->name,
                    'user_id' => $userId,
                ]);
                return true;
            }

            Log::channel('telegram')->error('Failed to send Telegram message', [
                'bot_name' => $this->name,
                'user_id' => $userId,
                'response' => $response->body(),
            ]);

            return false;
        } catch (RequestException $e) {
            Log::channel('telegram')->error('Failed to send Telegram message', [
                'bot_name' => $this->name,
                'user_id' => $userId,
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
        ?string $parseMode = null,
        bool $oneTime = false,
        bool $resize = true
    ): bool {
        try {
            $keyboard = new TelegramKeyboardDto($buttons, inline: false, oneTime: $oneTime, resize: $resize);

            $params = [
                'chat_id' => $userId,
                'text' => $text,
                'reply_markup' => json_encode($keyboard->toArray()),
            ];

            if ($parseMode) {
                $params['parse_mode'] = $parseMode;
            }

            $response = $this->http->post('/sendMessage', $params);

            if ($response->successful()) {
                Log::channel('telegram')->info('Telegram message with keyboard sent', [
                    'bot_name' => $this->name,
                    'user_id' => $userId,
                ]);
                return true;
            }

            Log::channel('telegram')->error('Failed to send Telegram message with keyboard', [
                'bot_name' => $this->name,
                'user_id' => $userId,
                'response' => $response->body(),
            ]);

            return false;
        } catch (RequestException $e) {
            Log::channel('telegram')->error('Failed to send Telegram message with keyboard', [
                'bot_name' => $this->name,
                'user_id' => $userId,
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
        ?string $parseMode = null
    ): bool {
        try {
            $params = [
                'chat_id' => $userId,
                'text' => $text,
                'reply_markup' => json_encode($keyboard->toArray()),
            ];

            if ($parseMode) {
                $params['parse_mode'] = $parseMode;
            }

            $response = $this->http->post('/sendMessage', $params);

            if ($response->successful()) {
                Log::channel('telegram')->info('Telegram message with inline keyboard sent', [
                    'bot_name' => $this->name,
                    'user_id' => $userId,
                ]);
                return true;
            }

            Log::channel('telegram')->error('Failed to send Telegram message with inline keyboard', [
                'bot_name' => $this->name,
                'user_id' => $userId,
                'response' => $response->body(),
            ]);

            return false;
        } catch (RequestException $e) {
            Log::channel('telegram')->error('Failed to send Telegram message with inline keyboard', [
                'bot_name' => $this->name,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Ответить на callback_query (убрать "часики" с кнопки)
     */
    public function answerCallbackQuery(
        string $callbackQueryId,
        ?string $text = null,
        bool $showAlert = false,
        ?string $url = null,
        int $cacheTime = 0
    ): bool {
        try {
            $params = [
                'callback_query_id' => $callbackQueryId,
            ];

            if ($text !== null) {
                $params['text'] = $text;
            }

            if ($showAlert) {
                $params['show_alert'] = $showAlert;
            }

            if ($url !== null) {
                $params['url'] = $url;
            }

            if ($cacheTime > 0) {
                $params['cache_time'] = $cacheTime;
            }

            $response = $this->http->post('/answerCallbackQuery', $params);

            if ($response->successful()) {
                Log::channel('telegram')->info('Callback query answered', [
                    'bot_name' => $this->name,
                    'callback_query_id' => $callbackQueryId,
                ]);
                return true;
            }

            Log::channel('telegram')->error('Failed to answer callback query', [
                'bot_name' => $this->name,
                'callback_query_id' => $callbackQueryId,
                'response' => $response->body(),
            ]);

            return false;
        } catch (RequestException $e) {
            Log::channel('telegram')->error('Failed to answer callback query', [
                'bot_name' => $this->name,
                'callback_query_id' => $callbackQueryId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

}
