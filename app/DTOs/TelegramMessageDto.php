<?php

namespace App\DTOs;

use App\Enums\TelegramMessageType;
use Illuminate\Contracts\Support\Arrayable;

class TelegramMessageDto implements Arrayable
{
    public readonly ?string $command;
    public readonly array $arguments;

    public function __construct(
        public readonly TelegramMessageType $messageType,
        public readonly string $text,
        public readonly string|int|null $userId = null,
        public readonly ?string $additionalData = null,
    ) {
        // Парсинг команды и аргументов для командных сообщений
        if ($messageType === TelegramMessageType::COMMAND) {
            $parts = array_values(array_filter(explode(' ', $text)));
            $this->command = ltrim($parts[0] ?? '', '/');
            $this->arguments = array_slice($parts, 1);
        } else {
            $this->command = null;
            $this->arguments = [];
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            messageType: TelegramMessageType::from($data['messageType'] ?? TelegramMessageType::TEXT->value),
            text: $data['text'],
            userId: $data['userId'] ?? null,
            additionalData: $data['additionalData'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'messageType' => $this->messageType->value,
            'text' => $this->text,
            'userId' => $this->userId,
            'additionalData' => $this->additionalData,
            'command' => $this->command,
            'arguments' => $this->arguments,
        ];
    }
}