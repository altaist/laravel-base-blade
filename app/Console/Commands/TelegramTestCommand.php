<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TelegramTestCommand extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Тестирование подключения к Telegram API';

    public function handle(TelegramService $telegram): int
    {
        $this->info('Проверка конфигурации...');
        
        $token = config('services.telegram.bot.token');
        $botName = config('services.telegram.bot.name');

        if (empty($token)) {
            $this->error('Ошибка конфигурации: Не указан TELEGRAM_BOT_TOKEN в .env');
            return Command::FAILURE;
        }

        if (empty($botName)) {
            $this->error('Ошибка конфигурации: Не указан TELEGRAM_BOT_NAME в .env');
            return Command::FAILURE;
        }

        $this->info('Конфигурация проверена:');
        $this->table(
            ['Параметр', 'Значение'],
            [
                ['Токен', substr($token, 0, 10) . '...' . substr($token, -5)],
                ['Имя бота', $botName],
            ]
        );

        $this->info('Отправка тестового сообщения в Telegram...');

        try {
            $message = sprintf(
                "Тестовое сообщение от %s\nВремя: %s\nОкружение: %s",
                config('app.name'),
                now()->format('Y-m-d H:i:s'),
                config('app.env')
            );

            $result = $telegram->sendMessageToBot($message);

            if ($result) {
                $this->info('✅ Тест успешно завершен:');
                $this->info('- Соединение с Telegram API установлено');
                $this->info('- Сообщение успешно отправлено');
                $this->info('- Бот получил сообщение');
                return Command::SUCCESS;
            }

            $this->error('❌ Ошибка при отправке сообщения:');
            $this->error('- Сообщение не было доставлено');
            $this->error('- Проверьте права бота и его активность');
            $this->warn('Попробуйте:');
            $this->warn('1. Проверить, что бот активен: https://t.me/' . $botName);
            $this->warn('2. Проверить права бота через @BotFather');
            $this->warn('3. Проверить правильность токена');
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('❌ Критическая ошибка при выполнении теста:');
            $this->error('- Сообщение об ошибке: ' . $e->getMessage());
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

            $this->warn('Рекомендации:');
            $this->warn('1. Проверить подключение к интернету');
            $this->warn('2. Проверить доступность api.telegram.org');
            $this->warn('3. Проверить корректность токена');
            $this->warn('4. Проверить логи: storage/logs/laravel.log');
            
            return Command::FAILURE;
        }
    }
}