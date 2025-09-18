<?php

namespace App\Services\Telegram\Commands\Main;

use App\DTOs\TelegramMessageDto;
use App\Models\AuthLink;
use App\Models\User;
use App\Services\AuthLinkService;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;

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
            // Пользователь существует - показываем приветствие с клавиатурой
            $text = "👋 Добро пожаловать, {$user->name}!\n\n" .
                "Вы уже авторизованы в системе. " .
                "Используйте кнопки ниже или команды для навигации.";
            
            // Создаем клавиатуру с основными командами в две колонки
            $keyboard = [
                [['text' => '👤 Профиль', 'callback_data' => '/profile']],
                [['text' => 'ℹ️ О боте', 'callback_data' => '/about']],
                [['text' => '🔐 Авторизация', 'callback_data' => '/auth']]
            ];
            
            $this->replyWithInlineKeyboard($message, $text, $keyboard, TelegramService::FORMAT_HTML);
            return;
        } else {
            // Пользователя нет - проверяем start_param для привязки
            if (!empty($message->arguments)) {
                $startParam = $message->arguments[0] ?? null;
                
                if (!empty($startParam)) {
                    // Пробуем привязать аккаунт по токену
                    $this->handleAccountBinding($message);
                    return;
                }
            }

            // Если нет start_param - показываем приветствие с ссылкой на регистрацию
            $text = "👋 Добро пожаловать!\n\n" .
                "Я бот для управления вашим аккаунтом. " .
                "Перейдите на сайт: " . config('app.url') . "\n\n" .
                "Для начала работы с личным кабинетом необходимо зарегистрироваться.\n\n";

            // Добавляем ссылку для авторизации если пользователь не найден
            try {
                $authLink = $this->createRegistrationLink($message);
                $loginUrl = route('auth-link.authenticate', $authLink['token']);
                
                $text .= "\n\n🔐 Для доступа к системе используйте ссылку для авторизации:\n" .
                    "{$loginUrl}\n\n" .
                    "Ссылка действительна 1 час.";
            } catch (\Exception $e) {
                // Если не удалось создать ссылку, продолжаем без неё
            }
            
            // Создаем клавиатуру для неавторизованных пользователей
            $keyboard = [
                [['text' => '🔐 Авторизация', 'callback_data' => '/auth']],
                [['text' => 'ℹ️ О боте', 'callback_data' => '/about']]
            ];
            
            $this->replyWithInlineKeyboard($message, $text, $keyboard, TelegramService::FORMAT_HTML);
            return;
        }

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }


}
