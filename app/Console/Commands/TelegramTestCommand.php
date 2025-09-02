<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;

class TelegramTestCommand extends Command
{
    protected $signature = 'telegram:test';
    protected $description = 'Тестирование подключения к Telegram API';

    public function handle(TelegramService $telegram): int
    {
        $this->info('Отправка тестового сообщения в Telegram...');

        try {
            $result = $telegram->sendMessageToBot('Тестовое сообщение от ' . config('app.name'));

            if ($result) {
                $this->info('OK: Сообщение успешно отправлено');
                return Command::SUCCESS;
            }

            $this->error('Ошибка: Не удалось отправить сообщение');
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error('Ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
