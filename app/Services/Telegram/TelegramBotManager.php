<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Log;

class TelegramBotManager
{
    /**
     * @var TelegramBot[]
     */
    private array $bots = [];

    public function __construct()
    {
        $this->registerBotsFromConfig();
    }

    /**
     * Зарегистрировать бота
     */
    public function registerBot(string $name, string $token, ?string $chatId = null, ?string $baseUrl = null): void
    {
        $this->bots[$name] = new TelegramBot($name, $token, $chatId, $baseUrl);
        
        Log::channel('telegram')->info('Telegram bot registered', [
            'bot_name' => $name,
            'has_chat_id' => !is_null($chatId),
            'base_url' => $baseUrl ?? 'https://api.telegram.org',
        ]);
    }

    /**
     * Получить бота по имени
     */
    public function getBot(string $name): TelegramBot
    {
        if (!isset($this->bots[$name])) {
            throw new \Exception("Bot '{$name}' not found");
        }

        return $this->bots[$name];
    }

    /**
     * Получить всех ботов
     */
    public function getAllBots(): array
    {
        return $this->bots;
    }

    /**
     * Получить бота по токену
     */
    public function getBotByToken(string $token): ?TelegramBot
    {
        foreach ($this->bots as $bot) {
            if ($bot->getToken() === $token) {
                return $bot;
            }
        }

        return null;
    }

    /**
     * Проверить, зарегистрирован ли бот
     */
    public function hasBot(string $name): bool
    {
        return isset($this->bots[$name]);
    }

    /**
     * Получить список имен ботов
     */
    public function getBotNames(): array
    {
        return array_keys($this->bots);
    }

    /**
     * Зарегистрировать ботов из конфигурации
     */
    private function registerBotsFromConfig(): void
    {
        $botsConfig = config('telegram.bots', []);

        foreach ($botsConfig as $name => $config) {
            if (!isset($config['token'])) {
                Log::channel('telegram')->error('Bot token not found in config', [
                    'bot_name' => $name,
                ]);
                continue;
            }

            $this->registerBot(
                $name,
                $config['token'],
                $config['chat_id'] ?? null,
                $config['base_url'] ?? null
            );
        }
    }
}
