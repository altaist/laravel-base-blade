<?php

namespace App\Services\Telegram\Commands;

use App\DTOs\TelegramMessageDto;
use App\Models\User;
use App\Services\AuthLinkService;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;

class AuthLinkCommand extends BaseTelegramCommand
{


    public function getName(): string
    {
        return 'auth';
    }

    public function getDescription(): string
    {
        return 'Создать ссылку для авторизации';
    }

    public function process(TelegramMessageDto $message): void
    {
        $authLinkService = app(AuthLinkService::class);
        
        try {
            // Ищем пользователя по telegram_id
            $user = User::where('telegram_id', $message->userId)->first();

            if ($user) {
                // Создаем ссылку для авторизации существующего пользователя
                $authLink = $authLinkService->generateAuthLink($user, [
                    'expires_in_minutes' => 15,
                    'ip_address' => null,
                    'user_agent' => 'Telegram Bot',
                    'author_id' => $user->id,
                ]);

            } else {
                // Создаем ссылку для регистрации нового пользователя
                $authLink = $authLinkService->generateRegistrationLink([
                    'telegram_id' => $message->userId,
                    'telegram_username' => null, // Можно добавить если есть
                ], [
                    'expires_in_minutes' => 60, // Регистрационные ссылки живут дольше
                    'ip_address' => null,
                    'user_agent' => 'Telegram Bot',
                    'author_id' => null,
                ]);
            }

            // Формируем ссылку
            $loginUrl = route('auth-link.authenticate', $authLink->token);

            // Формируем текст сообщения
            if ($user) {
                $text = "🔐 Ссылка для авторизации создана!\n\n" .
                    "Ссылка действительна 15 минут.\n" .
                    "Перейдите по ссылке для входа в систему:\n\n" .
                    "{$loginUrl}\n\n" .
                    "⚠️ Не передавайте эту ссылку третьим лицам!";
            } else {
                $text = "🔐 Ссылка для регистрации создана!\n\n" .
                    "Ссылка действительна 1 час.\n" .
                    "Перейдите по ссылке для создания аккаунта:\n\n" .
                    "{$loginUrl}\n\n" .
                    "⚠️ Не передавайте эту ссылку третьим лицам!";
            }

            $this->reply($message, $text, TelegramService::FORMAT_HTML);

        } catch (\Exception $e) {
            Log::channel('telegram')->error("Ошибка создания ссылки через Telegram", [
                'error' => $e->getMessage(),
                'telegram_id' => $message->userId,
            ]);

            $this->reply($message, "❌ Произошла ошибка при создании ссылки. Попробуйте позже.", TelegramService::FORMAT_HTML);
        }
    }
}
