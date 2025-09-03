<?php

namespace App\Contracts;

use App\DTOs\TelegramMessageDto;

interface TelegramBotCommandInterface
{
    /**
     * Обработать команду
     */
    public function process(TelegramMessageDto $message): void;

    /**
     * Получить название команды (без слеша в начале)
     */
    public function getName(): string;

    /**
     * Получить описание команды для помощи
     */
    public function getDescription(): string;

    /**
     * Проверить, может ли команда быть выполнена для данного сообщения
     */
    public function canProcess(TelegramMessageDto $message): bool;
}
