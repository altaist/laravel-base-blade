<?php

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;

class TelegramKeyboardDto implements Arrayable
{
    /**
     * @param array<array<array{text: string, callback_data?: string}>> $buttons Массив кнопок [[{text, callback_data}]]
     * @param bool $inline Использовать inline клавиатуру
     * @param bool $oneTime Скрывать клавиатуру после использования
     * @param bool $resize Автоматически изменять размер клавиатуры
     */
    public function __construct(
        public readonly array $buttons,
        public readonly bool $inline = true,
        public readonly bool $oneTime = false,
        public readonly bool $resize = true,
    ) {}

    public function toArray(): array
    {
        if ($this->inline) {
            return [
                'inline_keyboard' => $this->buttons
            ];
        }

        return [
            'keyboard' => $this->buttons,
            'one_time_keyboard' => $this->oneTime,
            'resize_keyboard' => $this->resize
        ];
    }

    /**
     * Создать простую inline клавиатуру с одной кнопкой
     */
    public static function singleButton(string $text, string $callbackData): self
    {
        return new self([
            [['text' => $text, 'callback_data' => $callbackData]]
        ]);
    }

    /**
     * Создать простую inline клавиатуру с несколькими кнопками в ряд
     * @param array<array{text: string, callback_data: string}> $buttons
     */
    public static function row(array $buttons): self
    {
        return new self([$buttons]);
    }
}
