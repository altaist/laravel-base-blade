<?php

namespace App\Console\Commands;

use App\Contracts\GptTransportInterface;
use App\DTOs\GptRequestDto;
use Illuminate\Console\Command;

class GptTestCommand extends Command
{
    protected $signature = 'gpt:test';
    protected $description = 'Тестирование подключения к GPT сервису';

    public function handle(GptTransportInterface $gpt): int
    {
        $this->info('Проверяем подключение к GPT...');

        try {
            $response = $gpt->requestText(new GptRequestDto(
                prompt: 'Напиши "OK" если ты меня понимаешь'
            ));

            $this->info('Ответ от GPT: ' . $response->response);
            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Ошибка: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
