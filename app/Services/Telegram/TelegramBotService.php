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
            try {
                if (!class_exists($commandClass)) {
                    Log::channel('telegram')->error("Telegram command class not found: {$commandClass}", [
                        'bot_type' => $this->botType,
                        'command_name' => $name,
                    ]);
                    continue;
                }

                // Проверяем, что класс реализует нужный интерфейс
                if (!is_subclass_of($commandClass, TelegramBotCommandInterface::class)) {
                    Log::channel('telegram')->error("Command class does not implement TelegramBotCommandInterface: {$commandClass}", [
                        'bot_type' => $this->botType,
                        'command_name' => $name,
                    ]);
                    continue;
                }

                /** @var TelegramBotCommandInterface $command */
                $command = app()->make($commandClass, ['telegram' => $this->telegram]);
                
                // Валидация имени команды
                if ($command->getName() !== $name) {
                    Log::channel('telegram')->warning(
                        "Telegram command name mismatch. Config: {$name}, Command: {$command->getName()}, Class: {$commandClass}",
                        [
                            'bot_type' => $this->botType,
                            'config_name' => $name,
                            'command_name' => $command->getName(),
                            'class' => $commandClass,
                        ]
                    );
                }

                $this->registerCommand($command);
                
            } catch (\Throwable $e) {
                Log::channel('telegram')->error("Failed to create telegram command: {$commandClass}", [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'bot_type' => $this->botType,
                    'command_name' => $name,
                ]);
                
                // Продолжаем регистрацию других команд
                continue;
            }
        }

        if (empty($this->commands)) {
            Log::channel('telegram')->warning("No telegram commands registered for bot type: {$this->botType}");
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
            // Ищем команду для обработки
            $command = $this->findCommand($message);

            if (!$command) {
                // Если команда не найдена, проверяем команду по умолчанию
                $defaultCommand = $this->commands['default'] ?? null;
                if ($defaultCommand && $defaultCommand->canProcess($message)) {
                    $defaultCommand->process($message);
                    Log::channel('telegram')->info('Telegram default command processed', [
                        'user_id' => $message->userId,
                        'message_type' => $message->messageType->value,
                    ]);
                    return;
                }

                // Если это команда, но неизвестная
                if ($message->command) {
                    $this->handleUnknownCommand($message);
                }
                return;
            }

            if (!$command->canProcess($message)) {
                Log::channel('telegram')->debug('Command cannot process message', [
                    'command' => $message->command,
                    'user_id' => $message->userId,
                    'bot_type' => $this->botType,
                ]);
                return;
            }

            $command->process($message);

            Log::channel('telegram')->info('Telegram command processed successfully', [
                'command' => $message->command,
                'user_id' => $message->userId,
                'bot_type' => $this->botType,
            ]);
        } catch (\Throwable $e) {
            Log::channel('telegram')->error('Failed to process Telegram command', [
                'command' => $message->command,
                'user_id' => $message->userId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'bot_type' => $this->botType,
            ]);

            // Отправляем сообщение об ошибке пользователю
            try {
                $this->telegram->sendMessageToUser(
                    $message->userId,
                    '❌ Произошла ошибка при выполнении команды. Попробуйте позже.',
                    TelegramService::FORMAT_HTML
                );
            } catch (\Throwable $sendError) {
                Log::channel('telegram')->error('Failed to send error message to user', [
                    'user_id' => $message->userId,
                    'error' => $sendError->getMessage(),
                    'file' => $sendError->getFile(),
                    'line' => $sendError->getLine(),
                    'bot_type' => $this->botType,
                ]);
            }
        }
    }

    /**
     * Найти команду для обработки сообщения
     */
    private function findCommand(TelegramMessageDto $message): ?TelegramBotCommandInterface
    {
        // Если это команда, ищем по имени команды
        if ($message->command) {
            return $this->commands[$message->command] ?? null;
        }
        
        // Если это не команда, возвращаем null (будет обработано командой по умолчанию)
        return null;
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
