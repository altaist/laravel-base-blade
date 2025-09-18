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
        $params = [
            'chat_id' => $userId,
            'text' => $text,
        ];

        if ($parseMode) {
            $params['parse_mode'] = $parseMode;
        }

        return $this->executeApiCall('/sendMessage', $params, 'Telegram message sent', [
            'user_id' => $userId,
        ]);
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
        $keyboard = new TelegramKeyboardDto($buttons, inline: false, oneTime: $oneTime, resize: $resize);

        $params = [
            'chat_id' => $userId,
            'text' => $text,
            'reply_markup' => json_encode($keyboard->toArray()),
        ];

        if ($parseMode) {
            $params['parse_mode'] = $parseMode;
        }

        return $this->executeApiCall('/sendMessage', $params, 'Telegram message with keyboard sent', [
            'user_id' => $userId,
        ]);
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
        $params = [
            'chat_id' => $userId,
            'text' => $text,
            'reply_markup' => json_encode($keyboard->toArray()),
        ];

        if ($parseMode) {
            $params['parse_mode'] = $parseMode;
        }

        return $this->executeApiCall('/sendMessage', $params, 'Telegram message with inline keyboard sent', [
            'user_id' => $userId,
        ]);
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

        return $this->executeApiCall('/answerCallbackQuery', $params, 'Callback query answered', [
            'callback_query_id' => $callbackQueryId,
        ]);
    }

    /**
     * Общий метод для выполнения API вызовов с обработкой ошибок
     */
    private function executeApiCall(string $endpoint, array $params, string $successMessage, array $logContext = []): bool
    {
        try {
            $response = $this->http->post($endpoint, $params);

            if ($response->successful()) {
                Log::channel('telegram')->info($successMessage, array_merge([
                    'bot_name' => $this->name,
                ], $logContext));
                return true;
            }

            Log::channel('telegram')->error("Failed to execute API call: {$endpoint}", array_merge([
                'bot_name' => $this->name,
                'response' => $response->body(),
            ], $logContext));

            return false;
        } catch (RequestException $e) {
            Log::channel('telegram')->error("Failed to execute API call: {$endpoint}", array_merge([
                'bot_name' => $this->name,
                'error' => $e->getMessage(),
            ], $logContext));

            return false;
        }
    }

}
