<?php

namespace App\Services\Telegram;

use App\DTOs\TelegramMessageDto;
use App\Services\Telegram\Commands\AboutCommand;
use App\Services\Telegram\Commands\StartCommand;
use App\Contracts\TelegramBotCommandInterface;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    /**
     * @var TelegramBotCommandInterface[]
     */
    private array $commands = [];

    public function __construct(
        private readonly TelegramService $telegram,
        ?array $commands = null
    ) {
        // Регистрируем базовые команды
        $this->registerCommand(new StartCommand($telegram));
        $this->registerCommand(new AboutCommand($telegram));

        // Регистрируем дополнительные команды
        if ($commands) {
            foreach ($commands as $command) {
                $this->registerCommand($command);
            }
        }
    }

    /**
     * Зарегистрировать новую команду
     */
    public function registerCommand(TelegramBotCommandInterface $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Обработать входящее сообщение
     */
    public function process(TelegramMessageDto $message): void
    {
        try {
            // Если это не команда, игнорируем
            if (!$message->command) {
                return;
            }

            $command = $this->findCommand($message);

            if (!$command) {
                $this->handleUnknownCommand($message);
                return;
            }

            if (!$command->canProcess($message)) {
                return;
            }

            $command->process($message);

            Log::info('Telegram command processed', [
                'command' => $message->command,
                'user_id' => $message->userId,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process Telegram command', [
                'command' => $message->command,
                'user_id' => $message->userId,
                'error' => $e->getMessage(),
            ]);

            // Отправляем сообщение об ошибке пользователю
            $this->telegram->sendMessageToUser(
                $message->userId,
                '❌ Произошла ошибка при выполнении команды. Попробуйте позже.',
                TelegramService::FORMAT_HTML
            );
        }
    }

    /**
     * Найти команду для обработки сообщения
     */
    private function findCommand(TelegramMessageDto $message): ?TelegramBotCommandInterface
    {
        return $this->commands[$message->command] ?? null;
    }

    /**
     * Обработать неизвестную команду
     */
    private function handleUnknownCommand(TelegramMessageDto $message): void
    {
        $this->telegram->sendMessageToUser(
            $message->userId,
            "❓ Неизвестная команда: /{$message->command}\n\n" .
            "Используйте /about чтобы увидеть список доступных команд.",
            TelegramService::FORMAT_HTML
        );
    }
}
