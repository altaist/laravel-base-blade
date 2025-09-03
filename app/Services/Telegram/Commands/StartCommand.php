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
        // Получаем start_param из arguments команды
        $startParam = $message->arguments[0] ?? null;
        if (empty($startParam)) {
            $this->reply($message, "❌ Неверный параметр для привязки.", TelegramService::FORMAT_HTML);
            return;
        }

        // Делегируем привязку в сервис
        $result = $this->authLinkService->bindTelegramAccount($startParam, $message->userId);

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
