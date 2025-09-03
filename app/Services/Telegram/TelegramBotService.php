<?php

namespace App\Services\Telegram;

use App\DTOs\TelegramMessageDto;

use App\Contracts\TelegramBotCommandInterface;
use Illuminate\Support\Facades\Log;

class TelegramBotService
{
    /**
     * @var TelegramBotCommandInterface[]
     */
    private array $commands = [];

    /**
     * @param TelegramService $telegram Сервис для работы с API Telegram
     * @param string $botType Тип бота из конфига (например, 'bot' или 'admin_bot')
     */
    public function __construct(
        private readonly TelegramService $telegram,
        private readonly string $botType
    ) {
        $this->registerCommands();
    }

    /**
     * Зарегистрировать команды из конфига для текущего бота
     */
    private function registerCommands(): void
    {
        $commands = config("telegram.{$this->botType}.commands", []);

        foreach ($commands as $name => $commandClass) {
            if (!class_exists($commandClass)) {
                Log::warning("Telegram command class not found: {$commandClass}");
                continue;
            }

            try {
                /** @var TelegramBotCommandInterface $command */
                $command = app()->make($commandClass, ['telegram' => $this->telegram]);
                
                if ($command->getName() !== $name) {
                    Log::warning(
                        "Telegram command name mismatch. " .
                        "Config: {$name}, Command: {$command->getName()}, " .
                        "Class: {$commandClass}"
                    );
                }

                $this->registerCommand($command);
            } catch (\Throwable $e) {
                Log::error("Failed to create telegram command: {$commandClass}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (empty($this->commands)) {
            Log::warning("No telegram commands registered for bot type: {$this->botType}");
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
