<?php

namespace App\Services\Telegram\Commands;

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
        // Проверяем, есть ли пользователь с таким telegram_id
        $user = User::where('telegram_id', $message->userId)->first();

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
            $this->addAuthLinkIfNeeded($message, $text);
            
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


    /**
     * Обработать привязку аккаунта по start_param
     */
    private function handleAccountBinding(TelegramMessageDto $message): void
    {
        $authLinkService = app(AuthLinkService::class);
        
        // Получаем start_param из arguments команды
        $startParam = $message->arguments[0] ?? null;
        if (empty($startParam)) {
            $this->reply($message, "❌ Неверный параметр для привязки.", TelegramService::FORMAT_HTML);
            return;
        }

        // Делегируем привязку в сервис
        $result = $authLinkService->bindTelegramAccount($startParam, $message->userId);

        if ($result['success']) {
            $text = "✅ Аккаунт успешно привязан!\n\n" .
                "Теперь вы можете получать уведомления и управлять аккаунтом через бота.";
        } else {
            $text = "❌ " . $result['message'];
        }

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }

    /**
     * Добавить ссылку для авторизации если пользователь не найден
     */
    private function addAuthLinkIfNeeded(TelegramMessageDto $message, string &$text): void
    {
        $authLinkService = app(AuthLinkService::class);
        
        try {
            // Ищем пользователя по telegram_id
            $user = User::where('telegram_id', $message->userId)->first();

            if (!$user) {
                // Создаем ссылку для регистрации
                $authLink = $authLinkService->generateRegistrationLink([
                    'telegram_id' => $message->userId,
                ], [
                    'expires_in_minutes' => 60,
                    'ip_address' => null,
                    'user_agent' => 'Telegram Bot',
                    'author_id' => null,
                ]);

                $loginUrl = route('auth-link.authenticate', $authLink->token);
                
                $text .= "\n\n🔐 Для доступа к системе используйте ссылку для авторизации:\n" .
                    "{$loginUrl}\n\n" .
                    "Ссылка действительна 1 час.";
            }
        } catch (\Exception $e) {
            Log::channel('telegram')->error("Ошибка создания ссылки в StartCommand", [
                'error' => $e->getMessage(),
                'telegram_id' => $message->userId,
            ]);
        }
    }
}
