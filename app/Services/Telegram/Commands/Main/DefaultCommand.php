<?php

namespace App\Services\Telegram\Commands\Main;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;

class DefaultCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'default';
    }

    public function getDescription(): string
    {
        return 'Команда по умолчанию для обычных сообщений';
    }

    public function canProcess(TelegramMessageDto $message): bool
    {
        // Эта команда обрабатывает все сообщения, которые не являются командами
        return $message->messageType === \App\Enums\TelegramMessageType::TEXT && 
               empty($message->command);
    }

    public function process(TelegramMessageDto $message): void
    {
        $text = "😊 Очень рады вашему сообщению!\n\n" .
                "Если у вас есть вопросы или нужна помощь, используйте команду /about для получения списка доступных команд.";

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
