<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook {--remove : Удалить текущий вебхук}';
    protected $description = 'Установка или удаление вебхука для Telegram';

    public function handle(): int
    {
        $token = config('services.telegram.bot.token');
        
        if (empty($token)) {
            $this->error('Ошибка: Не указан токен бота в конфигурации');
            return Command::FAILURE;
        }

        $baseUrl = "https://api.telegram.org/bot{$token}";

        if ($this->option('remove')) {
            return $this->removeWebhook($baseUrl);
        }

        return $this->setWebhook($baseUrl);
    }

    private function setWebhook(string $baseUrl): int
    {
        $appUrl = config('app.url');
        
        if (empty($appUrl)) {
            $this->error('Ошибка: Не указан URL приложения в конфигурации');
            return Command::FAILURE;
        }

        $webhookUrl = "{$appUrl}/api/telegram/webhook";

        $this->info("Установка вебхука на URL: {$webhookUrl}");

        try {
            $response = Http::get("{$baseUrl}/setWebhook", [
                'url' => $webhookUrl,
                'allowed_updates' => ['message', 'callback_query'],
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['ok'] ?? false)) {
                $this->info('OK: Вебхук успешно установлен');
                $this->info("Описание: " . ($result['description'] ?? ''));
                return Command::SUCCESS;
            }

            $this->error('Ошибка: Не удалось установить вебхук');
            $this->error("Ответ API: " . json_encode($result, JSON_UNESCAPED_UNICODE));
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function removeWebhook(string $baseUrl): int
    {
        $this->info('Удаление текущего вебхука...');

        try {
            $response = Http::get("{$baseUrl}/deleteWebhook");
            $result = $response->json();

            if ($response->successful() && ($result['ok'] ?? false)) {
                $this->info('OK: Вебхук успешно удален');
                return Command::SUCCESS;
            }

            $this->error('Ошибка: Не удалось удалить вебхук');
            $this->error("Ответ API: " . json_encode($result, JSON_UNESCAPED_UNICODE));
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
