<?php

namespace App\Services\Telegram\Commands;

use App\DTOs\TelegramMessageDto;
use App\Models\User;
use App\Services\Telegram\Base\BaseTelegramCommand;
use App\Services\Telegram\TelegramService;

class ProfileCommand extends BaseTelegramCommand
{
    public function getName(): string
    {
        return 'profile';
    }

    public function getDescription(): string
    {
        return 'Показать профиль пользователя';
    }

    public function process(TelegramMessageDto $message): void
    {
        $authLinkService = app(\App\Services\AuthLinkService::class);
        
        // Ищем пользователя по telegram_id
        $user = User::where('telegram_id', $message->userId)->first();

        if ($user) {
            // Пользователь найден - показываем профиль
            $text = "👤 <b>Ваш профиль</b>\n\n" .
                "<b>Имя:</b> " . ($user->name ?? 'Не указано') . "\n" .
                "<b>Email:</b> " . ($user->email ?? 'Не указан') . "\n" .
                "<b>Роль:</b> " . ucfirst($user->role ?? 'user') . "\n" .
                "<b>Дата регистрации:</b> " . $user->created_at->format('d.m.Y H:i') . "\n" .
                "<b>Telegram ID:</b> " . $user->telegram_id;
        } else {
            // Пользователь не найден - предлагаем регистрацию
            try {
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
                
                $text = "👤 <b>Профиль не найден</b>\n\n" .
                    "Для доступа к системе создайте аккаунт:\n" .
                    "{$loginUrl}\n\n" .
                    "Ссылка действительна 1 час.";
            } catch (\Exception $e) {
                $text = "❌ Ошибка при создании ссылки для регистрации";
            }
        }

        $this->reply($message, $text, TelegramService::FORMAT_HTML);
    }
}
