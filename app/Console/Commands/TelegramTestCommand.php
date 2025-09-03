<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TelegramTestCommand extends Command
{
    protected $signature = 'telegram:test 
        {--admin-only : Тестировать только админского бота}
        {--chat-id= : ID чата для отправки тестового сообщения через основного бота}';
    protected $description = 'Тестирование подключения к Telegram API';

    public function handle(TelegramService $telegram): int
    {
        $this->info('Проверка конфигурации...');
        
        $mainBotToken = config('telegram.bot.token');
        $mainBotName = config('telegram.bot.name');
        $adminBotToken = config('telegram.admin_bot.token');
        $adminBotName = config('telegram.admin_bot.name');
        $adminChatId = config('telegram.admin_bot.chat_id');
        $testChatId = $this->option('chat-id');

        $this->table(
            ['Параметр', 'Значение', 'Статус'],
            [
                [
                    'Основной бот (имя)', 
                    $mainBotName ?: 'Не указан', 
                    $mainBotName ? '✓' : '×'
                ],
                [
                    'Основной бот (токен)', 
                    $mainBotToken ? (substr($mainBotToken, 0, 10) . '...' . substr($mainBotToken, -5)) : 'Не указан',
                    $mainBotToken ? '✓' : '×'
                ],
                [
                    'Тестовый chat_id', 
                    $testChatId ?: 'Не указан',
                    $testChatId ? '✓' : '×'
                ],
                [
                    'Админский бот (имя)', 
                    $adminBotName ?: 'Не указан',
                    $adminBotName ? '✓' : '×'
                ],
                [
                    'Админский бот (токен)', 
                    $adminBotToken ? (substr($adminBotToken, 0, 10) . '...' . substr($adminBotToken, -5)) : 'Не указан',
                    $adminBotToken ? '✓' : '×'
                ],
                [
                    'ID админского чата', 
                    $adminChatId ?: 'Не указан',
                    $adminChatId ? '✓' : '×'
                ],
            ]
        );

        if (!$this->option('admin-only')) {
            if (empty($mainBotToken) || empty($mainBotName)) {
                $this->error('❌ Ошибка конфигурации основного бота:');
                $this->error('- Проверьте TELEGRAM_BOT_TOKEN и TELEGRAM_BOT_NAME в .env');
                return Command::FAILURE;
            }

            if (empty($testChatId)) {
                $this->error('❌ Не указан chat_id для тестирования основного бота:');
                $this->error('- Добавьте параметр --chat-id=YOUR_CHAT_ID');
                $this->info('Получить chat_id можно:');
                $this->info('1. Написать боту @userinfobot');
                $this->info('2. Переслать сообщение боту @RawDataBot');
                return Command::FAILURE;
            }
        }

        if (empty($adminBotToken) || empty($adminBotName) || empty($adminChatId)) {
            $this->error('❌ Ошибка конфигурации админского бота:');
            $this->error('- Проверьте TELEGRAM_ADMIN_BOT_TOKEN, TELEGRAM_ADMIN_BOT_NAME и TELEGRAM_ADMIN_CHAT_ID в .env');
            return Command::FAILURE;
        }

        $testsPassed = true;
        $message = sprintf(
            "🔍 Тестовое сообщение\n\nПриложение: %s\nВремя: %s\nОкружение: %s\nТип бота: %s",
            config('app.name'),
            now()->format('Y-m-d H:i:s'),
            config('app.env'),
            '%s'
        );

        // Тестируем админского бота
        $this->info("\nОтправка тестового сообщения через админского бота...");
        try {
            $adminResult = $telegram->sendAdminMessage(
                sprintf($message, 'Админский бот'),
                TelegramService::FORMAT_HTML
            );

            if ($adminResult) {
                $this->info('✅ Тест админского бота успешно завершен:');
                $this->info('- Соединение с Telegram API установлено');
                $this->info('- Сообщение успешно отправлено в админский чат');
            } else {
                $testsPassed = false;
                $this->error('❌ Ошибка при отправке сообщения через админского бота');
            }
        } catch (\Exception $e) {
            $testsPassed = false;
            $this->error('❌ Критическая ошибка при тестировании админского бота:');
            $this->error('- ' . $e->getMessage());
            $this->logException($e);
        }

        // Тестируем основного бота, если не указан флаг --admin-only
        if (!$this->option('admin-only')) {
            $this->info("\nОтправка тестового сообщения через основного бота...");
            try {
                $mainResult = $telegram->sendMessageToUser(
                    $testChatId,
                    sprintf($message, 'Основной бот'),
                    TelegramService::FORMAT_HTML
                );

                if ($mainResult) {
                    $this->info('✅ Тест основного бота успешно завершен:');
                    $this->info('- Соединение с Telegram API установлено');
                    $this->info(sprintf('- Сообщение успешно отправлено в чат %s', $testChatId));
                } else {
                    $testsPassed = false;
                    $this->error('❌ Ошибка при отправке сообщения через основного бота');
                }
            } catch (\Exception $e) {
                $testsPassed = false;
                $this->error('❌ Критическая ошибка при тестировании основного бота:');
                $this->error('- ' . $e->getMessage());
                $this->logException($e);
            }
        }

        if (!$testsPassed) {
            $this->warn("\nРекомендации при ошибках:");
            $this->warn('1. Проверить подключение к интернету');
            $this->warn('2. Проверить доступность api.telegram.org');
            $this->warn('3. Проверить корректность токенов');
            $this->warn('4. Проверить права ботов');
            $this->warn('5. Проверить правильность chat_id');
            $this->warn('6. Проверить логи: storage/logs/laravel.log');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function logException(\Exception $e): void
    {
        $this->error('- Файл: ' . $e->getFile());
        $this->error('- Строка: ' . $e->getLine());
            
        if ($e->getPrevious()) {
            $this->error('Предыдущая ошибка:');
            $this->error('- ' . $e->getPrevious()->getMessage());
        }

        $this->warn('Трейс ошибки:');
        foreach (array_slice($e->getTrace(), 0, 3) as $index => $trace) {
            $this->warn(sprintf(
                "%d. %s::%s() строка %d",
                $index + 1,
                $trace['class'] ?? '',
                $trace['function'] ?? '',
                $trace['line'] ?? 0
            ));
        }

        Log::error('Telegram test command failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}