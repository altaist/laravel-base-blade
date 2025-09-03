<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramService;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramServiceCommand extends Command
{
    protected $signature = 'telegram:run 
        {--bot=bot : Тип бота (bot или admin_bot)}
        {--interval=5 : Интервал между запросами в секундах}';

    protected $description = 'Сервис для периодического получения сообщений от Telegram';

    private int $lastOffset = 0;

    public function handle(): int
    {
        $botType = $this->option('bot');
        $interval = (int) $this->option('interval');
        
        $token = config("telegram.{$botType}.token");
        
        if (empty($token)) {
            $this->error("Ошибка: Не указан токен для бота типа '{$botType}' в конфигурации");
            return Command::FAILURE;
        }

        $baseUrl = "https://api.telegram.org/bot{$token}";

        // Сохраняем текущий вебхук
        $currentWebhook = $this->getCurrentWebhook($baseUrl);
        
        // Удаляем вебхук для возможности использования getUpdates
        $this->info("Удаление текущего вебхука...");
        $this->deleteWebhook($baseUrl);

        $this->info("Запуск сервиса для бота типа: {$botType}");
        $this->info("Интервал запросов: {$interval} секунд");
        $this->info("Для остановки нажмите Ctrl+C");

        try {
            while (true) {
                try {
                    $this->processUpdates($baseUrl);
                    sleep($interval);
                } catch (RequestException $e) {
                    // Игнорируем таймауты и сетевые ошибки
                    if (str_contains($e->getMessage(), 'timeout') || $e->getCode() === 28) {
                        $this->warn("Таймаут запроса, продолжаем работу...");
                        sleep($interval);
                        continue;
                    }
                    
                    Log::channel('telegram')->error('Сетевая ошибка в сервисе Telegram', [
                        'bot_type' => $botType,
                        'error' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]);
                    
                    $this->error("Сетевая ошибка: {$e->getMessage()}");
                    sleep($interval);
                } catch (\Exception $e) {
                    Log::channel('telegram')->error('Критическая ошибка в сервисе Telegram', [
                        'bot_type' => $botType,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    $this->error("Критическая ошибка: {$e->getMessage()}");
                    sleep($interval);
                }
            }
        } finally {
            // Восстанавливаем вебхук при завершении команды
            $this->info("\nВосстановление вебхука...");
            if ($currentWebhook) {
                $this->restoreWebhook($baseUrl, $currentWebhook);
            }
        }

        return Command::SUCCESS;
    }

    private function processUpdates(string $baseUrl): void
    {
        try {
            $response = Http::timeout(10)->get("{$baseUrl}/getUpdates", [
                'offset' => $this->lastOffset + 1,
                'timeout' => 5, // Короткий таймаут для Telegram API
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to get updates from Telegram: ' . $response->body());
            }

            $updates = $response->json('result', []);
            
            if (empty($updates)) {
                return;
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Игнорируем таймауты - это нормально для long polling
            if (str_contains($e->getMessage(), 'timeout') || $e->getCode() === 28) {
                return;
            }
            throw $e;
        }

        $processed = 0;
        $botType = $this->getBotTypeFromUrl($baseUrl);
        $telegram = app(TelegramService::class);
        $botService = app(TelegramBotService::class, [
            'telegram' => $telegram,
            'botType' => $botType
        ]);

        foreach ($updates as $update) {
            try {
                // Используем существующий метод TelegramService для создания DTO
                $telegram->handleIncomingMessage($update, $botType);
                
                $processed++;
                
                // Обновляем offset для следующего запроса
                $this->lastOffset = max($this->lastOffset, $update['update_id']);
                
                Log::channel('telegram')->info('Сообщение обработано', [
                    'update_id' => $update['update_id'],
                    'bot_type' => $botType,
                ]);
            } catch (\Exception $e) {
                Log::channel('telegram')->error('Ошибка обработки сообщения', [
                    'update_id' => $update['update_id'] ?? 'unknown',
                    'bot_type' => $botType,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($processed > 0) {
            $this->info("Обработано сообщений: {$processed}");
        }
    }

    private function getBotTypeFromUrl(string $baseUrl): string
    {
        // Определяем тип бота по токену в URL
        $token = basename($baseUrl);
        
        foreach (config('telegram') as $type => $config) {
            if (isset($config['token']) && $config['token'] === $token) {
                return $type;
            }
        }
        
        return 'bot'; // fallback
    }



    private function getCurrentWebhook(string $baseUrl): ?array
    {
        try {
            $response = Http::get("{$baseUrl}/getWebhookInfo");
            $result = $response->json();
            
            if ($response->successful() && ($result['ok'] ?? false)) {
                $info = $result['result'] ?? [];
                if (!empty($info['url'])) {
                    return $info;
                }
            }
        } catch (\Exception $e) {
            $this->warn("Не удалось получить информацию о текущем вебхуке: {$e->getMessage()}");
        }
        
        return null;
    }

    private function deleteWebhook(string $baseUrl): void
    {
        try {
            $response = Http::get("{$baseUrl}/deleteWebhook");
            $result = $response->json();
            
            if ($response->successful() && ($result['ok'] ?? false)) {
                $this->info("Вебхук успешно удален");
            } else {
                $this->warn("Не удалось удалить вебхук: " . json_encode($result));
            }
        } catch (\Exception $e) {
            $this->warn("Ошибка при удалении вебхука: {$e->getMessage()}");
        }
    }

    private function restoreWebhook(string $baseUrl, array $webhookInfo): void
    {
        try {
            $url = $webhookInfo['url'] ?? null;
            if (!$url) {
                $this->warn("Не удалось восстановить вебхук: URL не найден");
                return;
            }

            $response = Http::get("{$baseUrl}/setWebhook", [
                'url' => $url,
                'allowed_updates' => $webhookInfo['allowed_updates'] ?? ['message', 'callback_query'],
            ]);

            $result = $response->json();
            
            if ($response->successful() && ($result['ok'] ?? false)) {
                $this->info("Вебхук успешно восстановлен: {$url}");
            } else {
                $this->warn("Не удалось восстановить вебхук: " . json_encode($result));
            }
        } catch (\Exception $e) {
            $this->warn("Ошибка при восстановлении вебхука: {$e->getMessage()}");
        }
    }
}
