<?php

namespace App\Services\Telegram\Commands;

use App\DTOs\TelegramMessageDto;
use App\Models\User;
use App\Services\AuthLinkService;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;

class AboutCommand extends BaseTelegramCommand
{


    public function getName(): string
    {
        return 'about';
    }

    public function getDescription(): string
    {
        return 'Информация о боте и доступных командах';
    }

    public function process(TelegramMessageDto $message): void
    {
        $text = "ℹ️ <b>О боте</b>\n\n" .
            "Я помогаю управлять вашим аккаунтом и предоставляю доступ к различным функциям.\n\n" .
            "<b>Доступные команды:</b>\n" .
            "/start - Начать работу с ботом\n" .
            "/about - Показать это сообщение\n" .
            "/auth - Создать ссылку для авторизации\n";

        // Добавляем ссылку для авторизации если пользователь не найден
        // $this->addAuthLinkIfNeeded($message, $text);

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
                
                $text .= "\n\n🔐 <b>Для доступа к системе:</b>\n" .
                    "Создана ссылка для авторизации:\n" .
                    "{$loginUrl}\n\n" .
                    "Ссылка действительна 1 час.";
            }
        } catch (\Exception $e) {
            Log::channel('telegram')->error("Ошибка создания ссылки в AboutCommand", [
                'error' => $e->getMessage(),
                'telegram_id' => $message->userId,
            ]);
        }
    }
}
