<?php

namespace App\Services\Telegram\Commands\Main;

use App\DTOs\TelegramMessageDto;
use App\Models\User;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;

class StartCommand extends BaseTelegramCommand
{


    public function getName(): string
    {
        return 'start';
    }

    public function getDescription(): string
    {
        return 'Начать работу с ботом';
    }

    public function process(TelegramMessageDto $message): void
    {
        // Проверяем, есть ли пользователь с таким telegram_id для этого бота
        $user = $this->findUser($message);

        if ($user) {
            $this->handleAuthenticatedUser($message, $user);
        } else {
            $this->handleUnauthenticatedUser($message);
        }
    }

    /**
     * Обработать авторизованного пользователя
     */
    private function handleAuthenticatedUser(TelegramMessageDto $message, User $user): void
    {
        $text = "👋 Добро пожаловать, {$user->name}!\n\n" .
            "Вы уже авторизованы в системе. " .
            "Используйте кнопки ниже или команды для навигации.";
        
        $keyboard = $this->getAuthenticatedUserKeyboard();
        $this->replyWithInlineKeyboard($message, $text, $keyboard, TelegramService::FORMAT_HTML);
    }

    /**
     * Обработать неавторизованного пользователя
     */
    private function handleUnauthenticatedUser(TelegramMessageDto $message): void
    {
        // Проверяем start_param для привязки аккаунта
        if (!empty($message->arguments)) {
            $startParam = $message->arguments[0] ?? null;
            
            if (!empty($startParam)) {
                $this->handleAccountBinding($message);
                return;
            }
        }

        $text = $this->buildWelcomeText($message);
        $keyboard = $this->getUnauthenticatedUserKeyboard();
        
        $this->replyWithInlineKeyboard($message, $text, $keyboard, TelegramService::FORMAT_HTML);
    }

    /**
     * Создать текст приветствия для неавторизованного пользователя
     */
    private function buildWelcomeText(TelegramMessageDto $message): string
    {
        $text = "👋 Добро пожаловать!\n\n" .
            "Я бот для управления вашим аккаунтом. " .
            "Перейдите на сайт: " . config('app.url') . "\n\n" .
            "Для начала работы с личным кабинетом необходимо зарегистрироваться.\n\n";

        // Добавляем ссылку для авторизации если пользователь не найден
        try {
            $authLink = $this->createRegistrationLink($message);
            $loginUrl = route('auth-link.authenticate', $authLink->token);
            
            $text .= "\n\n🔐 Для доступа к системе используйте ссылку для авторизации:\n" .
                "{$loginUrl}\n\n" .
                "Ссылка действительна 1 час.";
        } catch (\Exception $e) {
            // Если не удалось создать ссылку, продолжаем без неё
        }

        return $text;
    }

    /**
     * Получить клавиатуру для авторизованного пользователя
     */
    private function getAuthenticatedUserKeyboard(): array
    {
        return [
            [['text' => '👤 Профиль', 'callback_data' => '/profile']],
            [['text' => 'ℹ️ О боте', 'callback_data' => '/about']],
            [['text' => '🔐 Авторизация', 'callback_data' => '/auth']]
        ];
    }

    /**
     * Получить клавиатуру для неавторизованного пользователя
     */
    private function getUnauthenticatedUserKeyboard(): array
    {
        return [
            [['text' => '🔐 Авторизация', 'callback_data' => '/auth']],
            [['text' => 'ℹ️ О боте', 'callback_data' => '/about']]
        ];
    }


}
