<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramSetupCommand extends Command
{
    protected $signature = 'telegram:setup 
        {url? : URL для вебхука (например, https://your-domain.com)}
        {--remove : Удалить все вебхуки}
        {--info : Показать текущие настройки вебхуков}';

    protected $description = 'Установка или удаление вебхуков для всех Telegram ботов';

    public function handle(): int
    {
        $url = $this->argument('url') ?? config('app.url');
        
        if (empty($url)) {
            $this->error('Ошибка: Укажите URL для вебхука в аргументе команды или в APP_URL');
            $this->info('Пример: php artisan telegram:setup https://your-domain.com');
            return Command::FAILURE;
        }

        // Убираем слеш в конце URL если есть
        $url = rtrim($url, '/');

        if ($this->option('info')) {
            return $this->showWebhookInfo($url);
        }

        if ($this->option('remove')) {
            return $this->removeAllWebhooks();
        }

        return $this->setupAllWebhooks($url);
    }

    private function setupAllWebhooks(string $baseUrl): int
    {
        $this->info("Настройка вебхуков для всех ботов...");
        $this->info("Базовый URL: {$baseUrl}");

        $bots = config('telegram');
        $successCount = 0;
        $totalBots = 0;

        foreach ($bots as $botType => $config) {
            // Пропускаем секции без токена (например, commands)
            if (!isset($config['token'])) {
                continue;
            }

            $totalBots++;
            $this->info("\nНастройка бота типа: {$botType}");

            try {
                $webhookUrl = "{$baseUrl}/api/telegram/{$botType}/webhook";
                $this->info("URL вебхука: {$webhookUrl}");

                $response = Http::get("https://api.telegram.org/bot{$config['token']}/setWebhook", [
                    'url' => $webhookUrl,
                    'allowed_updates' => ['message', 'callback_query'],
                ]);

                $result = $response->json();

                if ($response->successful() && ($result['ok'] ?? false)) {
                    $this->info("✅ Вебхук успешно установлен");
                    $this->info("Описание: " . ($result['description'] ?? ''));
                    $successCount++;
                } else {
                    $this->error("❌ Не удалось установить вебхук");
                    $this->error("Ответ API: " . json_encode($result, JSON_UNESCAPED_UNICODE));
                }
            } catch (\Exception $e) {
                $this->error("❌ Ошибка: " . $e->getMessage());
            }
        }

        $this->info("\n📊 Результат настройки:");
        $this->info("Успешно настроено: {$successCount}/{$totalBots} ботов");

        if ($successCount === $totalBots) {
            $this->info("🎉 Все вебхуки успешно настроены!");
            return Command::SUCCESS;
        } else {
            $this->warn("⚠️ Некоторые вебхуки не удалось настроить");
            return Command::FAILURE;
        }
    }

    private function removeAllWebhooks(): int
    {
        $this->info("Удаление всех вебхуков...");

        $bots = config('telegram');
        $successCount = 0;
        $totalBots = 0;

        foreach ($bots as $botType => $config) {
            if (!isset($config['token'])) {
                continue;
            }

            $totalBots++;
            $this->info("\nУдаление вебхука для бота типа: {$botType}");

            try {
                $response = Http::get("https://api.telegram.org/bot{$config['token']}/deleteWebhook");
                $result = $response->json();

                if ($response->successful() && ($result['ok'] ?? false)) {
                    $this->info("✅ Вебхук успешно удален");
                    $successCount++;
                } else {
                    $this->error("❌ Не удалось удалить вебхук");
                    $this->error("Ответ API: " . json_encode($result, JSON_UNESCAPED_UNICODE));
                }
            } catch (\Exception $e) {
                $this->error("❌ Ошибка: " . $e->getMessage());
            }
        }

        $this->info("\n📊 Результат удаления:");
        $this->info("Успешно удалено: {$successCount}/{$totalBots} вебхуков");

        if ($successCount === $totalBots) {
            $this->info("🎉 Все вебхуки успешно удалены!");
            return Command::SUCCESS;
        } else {
            $this->warn("⚠️ Некоторые вебхуки не удалось удалить");
            return Command::FAILURE;
        }
    }

    private function showWebhookInfo(string $baseUrl): int
    {
        $this->info("Информация о текущих вебхуках:");

        $bots = config('telegram');
        $hasWebhooks = false;

        foreach ($bots as $botType => $config) {
            if (!isset($config['token'])) {
                continue;
            }

            $this->info("\n🔍 Бот типа: {$botType}");
            $this->info("Имя: " . ($config['name'] ?? 'Не указан'));

            try {
                $response = Http::get("https://api.telegram.org/bot{$config['token']}/getWebhookInfo");
                $result = $response->json();

                if ($response->successful() && ($result['ok'] ?? false)) {
                    $info = $result['result'] ?? [];
                    
                    if (!empty($info['url'])) {
                        $hasWebhooks = true;
                        $this->info("✅ Вебхук активен:");
                        $this->table(
                            ['Параметр', 'Значение'],
                            [
                                ['URL', $info['url']],
                                ['Последняя ошибка', $info['last_error_message'] ?? 'Нет'],
                                ['Последняя синхронизация', $info['last_synchronization_error_date'] ?? 'Нет'],
                                ['Максимальные подключения', $info['max_connections'] ?? '40'],
                                ['Разрешенные обновления', json_encode($info['allowed_updates'] ?? [])],
                            ]
                        );
                    } else {
                        $this->info("❌ Вебхук не установлен");
                    }
                } else {
                    $this->error("❌ Не удалось получить информацию о вебхуке");
                    $this->error("Ответ API: " . json_encode($result, JSON_UNESCAPED_UNICODE));
                }
            } catch (\Exception $e) {
                $this->error("❌ Ошибка: " . $e->getMessage());
            }
        }

        if (!$hasWebhooks) {
            $this->warn("\n⚠️ Ни один вебхук не установлен");
            $this->info("Для установки используйте: php artisan telegram:setup {$baseUrl}");
        }

        return Command::SUCCESS;
    }
}
