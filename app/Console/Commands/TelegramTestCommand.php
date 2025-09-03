<?php

namespace App\Console\Commands;

use App\Services\Telegram\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TelegramTestCommand extends Command
{
    protected $signature = 'telegram:test 
        {--bot=bot : Тип бота для тестирования (bot или admin_bot)}
        {--chat-id= : ID чата для отправки тестового сообщения}';
    protected $description = 'Тестирование подключения к Telegram API';

    public function handle(TelegramService $telegram): int
    {
        $this->info('Проверка конфигурации...');
        
        $botType = $this->option('bot');
        $token = config("telegram.{$botType}.token");
        $name = config("telegram.{$botType}.name");
        $defaultChatId = $botType === 'admin_bot' ? config('telegram.admin_bot.chat_id') : null;
        $chatId = $this->option('chat-id') ?: $defaultChatId;

        $this->table(
            ['Параметр', 'Значение', 'Статус'],
            [
                [
                    'Тип бота',
                    $botType,
                    '✓'
                ],
                [
                    'Имя бота', 
                    $name ?: 'Не указан', 
                    $name ? '✓' : '×'
                ],
                [
                    'Токен бота', 
                    $token ? (substr($token, 0, 10) . '...' . substr($token, -5)) : 'Не указан',
                    $token ? '✓' : '×'
                ],
                [
                    'ID чата', 
                    $chatId ?: 'Не указан',
                    $chatId ? '✓' : '×'
                ],
            ]
        );

        if (empty($token) || empty($name)) {
            $this->error("❌ Ошибка конфигурации бота типа '{$botType}':");
            $this->error("- Проверьте настройки в .env и config/telegram.php");
            return Command::FAILURE;
        }

        if (empty($chatId)) {
            $this->error('❌ Не указан chat_id для тестирования:');
            if ($botType === 'admin_bot') {
                $this->error('- Проверьте TELEGRAM_ADMIN_CHAT_ID в .env');
            } else {
                $this->error('- Добавьте параметр --chat-id=YOUR_CHAT_ID');
                $this->info('Получить chat_id можно:');
                $this->info('1. Написать боту @userinfobot');
                $this->info('2. Переслать сообщение боту @RawDataBot');
            }
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

        $this->info("\nОтправка тестового сообщения...");
        try {
            $result = $botType === 'admin_bot'
                ? $telegram->sendAdminMessage(
                    sprintf($message, 'Админский бот'),
                    TelegramService::FORMAT_HTML,
                    $chatId
                )
                : $telegram->sendMessageToUser(
                    $chatId,
                    sprintf($message, 'Основной бот'),
                    TelegramService::FORMAT_HTML
                );

            if ($result) {
                $this->info("✅ Тест бота типа '{$botType}' успешно завершен:");
                $this->info('- Соединение с Telegram API установлено');
                $this->info(sprintf('- Сообщение успешно отправлено в чат %s', $chatId));
            } else {
                $testsPassed = false;
                $this->error("❌ Ошибка при отправке сообщения через бота типа '{$botType}'");
            }
        } catch (\Exception $e) {
            $testsPassed = false;
            $this->error("❌ Критическая ошибка при тестировании бота типа '{$botType}':");
            $this->error('- ' . $e->getMessage());
            $this->logException($e);
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