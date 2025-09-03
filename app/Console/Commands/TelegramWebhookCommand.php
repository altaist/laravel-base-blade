<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramWebhookCommand extends Command
{
    protected $signature = 'telegram:webhook 
        {url? : URL для вебхука (например, https://your-domain.com)}
        {--bot=bot : Тип бота (bot или admin_bot)}
        {--remove : Удалить текущий вебхук}
        {--info : Показать текущие настройки вебхука}';

    protected $description = 'Установка или удаление вебхука для Telegram';

    public function handle(): int
    {
        $botType = $this->option('bot');
        $token = config("telegram.{$botType}.token");
        
        if (empty($token)) {
            $this->error("Ошибка: Не указан токен для бота типа '{$botType}' в конфигурации");
            return Command::FAILURE;
        }

        $baseUrl = "https://api.telegram.org/bot{$token}";

        if ($this->option('info')) {
            return $this->getWebhookInfo($baseUrl);
        }

        if ($this->option('remove')) {
            return $this->removeWebhook($baseUrl);
        }

        return $this->setWebhook($baseUrl);
    }

    private function setWebhook(string $baseUrl): int
    {
        $url = $this->argument('url') ?? config('app.url');
        
        if (empty($url)) {
            $this->error('Ошибка: Укажите URL для вебхука в аргументе команды или в APP_URL');
            $this->info('Пример: php artisan telegram:webhook https://your-domain.com');
            return Command::FAILURE;
        }

        // Убираем слеш в конце URL если есть
        $url = rtrim($url, '/');
        $webhookUrl = "{$url}/api/telegram/{$this->option('bot')}/webhook";

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

    private function getWebhookInfo(string $baseUrl): int
    {
        $this->info('Получение информации о текущем вебхуке...');

        try {
            $response = Http::get("{$baseUrl}/getWebhookInfo");
            $result = $response->json();

            if ($response->successful() && ($result['ok'] ?? false)) {
                $info = $result['result'] ?? [];
                
                $this->info('Текущие настройки вебхука:');
                $this->table(
                    ['Параметр', 'Значение'],
                    [
                        ['URL', $info['url'] ?? 'Не установлен'],
                        ['Последняя ошибка', $info['last_error_message'] ?? 'Нет'],
                        ['Последняя синхронизация', $info['last_synchronization_error_date'] ?? 'Нет'],
                        ['Максимальные подключения', $info['max_connections'] ?? '40'],
                        ['Разрешенные обновления', json_encode($info['allowed_updates'] ?? [])],
                    ]
                );
                return Command::SUCCESS;
            }

            $this->error('Ошибка: Не удалось получить информацию о вебхуке');
            $this->error("Ответ API: " . json_encode($result, JSON_UNESCAPED_UNICODE));
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}