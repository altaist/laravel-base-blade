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
    public function __construct(
        private AuthLinkService $authLinkService
    ) {}

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
        // Проверяем start_param для привязки аккаунта
        if (!empty($message->arguments)) {
            $this->handleAccountBinding($message);
            return;
        }

        $text = "👋 Добро пожаловать!\n\n" .
            "Я бот для управления вашим аккаунтом. " .
            "Используйте команду /about чтобы узнать больше о моих возможностях.";

        // Добавляем ссылку для авторизации если пользователь не найден
        $this->addAuthLinkIfNeeded($message, $text);

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }

    /**
     * Обработать привязку аккаунта по start_param
     */
    private function handleAccountBinding(TelegramMessageDto $message): void
    {
        try {
            // Получаем start_param из arguments команды
            $startParam = $message->arguments[0] ?? null;
            if (empty($startParam)) {
                $this->reply($message, "❌ Неверный параметр для привязки.", TelegramService::FORMAT_HTML);
                return;
            }

            // Ищем активную ссылку по токену
            $authLink = AuthLink::where('token', $startParam)
                ->where('user_id', '!=', null) // Только для существующих пользователей
                ->active()
                ->first();

            if (!$authLink) {
                $this->reply($message, "❌ Ссылка недействительна или истекла.", TelegramService::FORMAT_HTML);
                return;
            }

            // Получаем пользователя
            $user = $authLink->user;
            if (!$user) {
                $this->reply($message, "❌ Пользователь не найден.", TelegramService::FORMAT_HTML);
                return;
            }

            // Привязываем Telegram аккаунт к пользователю
            $user->update([
                'telegram_id' => $message->userId,
                'telegram_username' => null, // Username будет добавлен позже если нужно
            ]);

            // Удаляем использованную ссылку
            $authLink->delete();

            $text = "✅ Аккаунт успешно привязан!\n\n" .
                "Теперь вы можете получать уведомления и управлять аккаунтом через бота.";

            $this->reply($message, $text, TelegramService::FORMAT_HTML);

        } catch (\Exception $e) {
            Log::channel('telegram')->error("Ошибка привязки Telegram аккаунта", [
                'error' => $e->getMessage(),
                'start_param' => $startParam,
                'telegram_id' => $message->userId,
            ]);

            $this->reply($message, "❌ Произошла ошибка при привязке аккаунта. Попробуйте позже.", TelegramService::FORMAT_HTML);
        }
    }

    /**
     * Добавить ссылку для авторизации если пользователь не найден
     */
    private function addAuthLinkIfNeeded(TelegramMessageDto $message, string &$text): void
    {
        try {
            // Ищем пользователя по telegram_id
            $user = User::where('telegram_id', $message->userId)->first();

            if (!$user) {
                // Создаем ссылку для регистрации
                $authLink = $this->authLinkService->generateRegistrationLink([
                    'telegram_id' => $message->userId,
                ], [
                    'expires_in_minutes' => 60,
                    'ip_address' => null,
                    'user_agent' => 'Telegram Bot',
                    'author_id' => null,
                ]);

                $loginUrl = route('auth-link.authenticate', $authLink->token);
                
                $text .= "\n\n🔐 Для доступа к системе создайте ссылку для авторизации:\n" .
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
