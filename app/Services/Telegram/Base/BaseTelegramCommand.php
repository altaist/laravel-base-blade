<?php

namespace App\Services\Telegram\Base;

use App\DTOs\TelegramKeyboardDto;
use App\DTOs\TelegramMessageDto;
use App\Contracts\TelegramBotCommandInterface;
use App\Services\Telegram\TelegramService;
use App\Services\AuthLinkService;
use App\Models\User;

abstract class BaseTelegramCommand implements TelegramBotCommandInterface
{
    public function __construct(
        protected readonly TelegramService $telegram
    ) {
    }

    /**
     * Проверить, может ли команда быть выполнена для данного сообщения
     */
    public function canProcess(TelegramMessageDto $message): bool
    {
        // Проверяем, что это команда и она соответствует текущей
        // Также проверяем callback_query с командой
        return $message->command === $this->getName();
    }

    /**
     * Отправить ответ пользователю
     */
    protected function reply(TelegramMessageDto $message, string $text, ?string $parseMode = TelegramService::FORMAT_NONE): void
    {
        $this->telegram->sendMessageToUser($message->userId, $text, $parseMode);
        $this->answerCallbackQuery($message);
    }

    /**
     * Отправить ответ с клавиатурой
     */
    protected function replyWithKeyboard(
        TelegramMessageDto $message,
        string $text,
        array $buttons,
        ?string $parseMode = TelegramService::FORMAT_NONE,
        bool $oneTime = false,
        bool $resize = true
    ): void {
        $this->telegram->sendMessageWithKeyboard(
            $message->userId,
            $text,
            $buttons,
            $parseMode,
            $oneTime,
            $resize
        );
        $this->answerCallbackQuery($message);
    }

    /**
     * Отправить ответ с inline клавиатурой
     */
    protected function replyWithInlineKeyboard(
        TelegramMessageDto $message,
        string $text,
        array $buttons,
        ?string $parseMode = TelegramService::FORMAT_NONE
    ): void {
        $keyboard = new TelegramKeyboardDto($buttons, inline: true);
        $this->telegram->sendMessageWithInlineKeyboard(
            $message->userId,
            $text,
            $keyboard,
            $parseMode
        );
        $this->answerCallbackQuery($message);
    }

    /**
     * Ответить на callback_query (убрать "часики" с кнопки)
     */
    protected function answerCallbackQuery(
        TelegramMessageDto $message,
        ?string $text = null,
        bool $showAlert = false
    ): void {
        if ($message->messageType->value !== 'callback_query') {
            return;
        }

        $additionalData = json_decode($message->additionalData ?? '{}', true);
        $callbackQueryId = $additionalData['callback_query']['id'] ?? null;

        if ($callbackQueryId) {
            $this->telegram->answerCallbackQuery(
                $callbackQueryId,
                $text,
                $showAlert,
                null,
                0,
                $message->botId
            );
        }
    }

    /**
     * Найти пользователя по Telegram ID для конкретного бота
     */
    protected function findUser(TelegramMessageDto $message): ?User
    {
        return User::whereHas('telegramBots', function($query) use ($message) {
            $query->where('telegram_id', $message->userId)
                  ->where('bot_name', $message->botId);
        })->first();
    }

    /**
     * Найти пользователя и проверить авторизацию
     * Если пользователь не найден, отправляет сообщение об авторизации
     */
    protected function requireUser(TelegramMessageDto $message): ?User
    {
        $user = $this->findUser($message);
        
        if (!$user) {
            $this->sendUnauthorizedMessage($message);
            return null;
        }
        
        return $user;
    }

    /**
     * Отправить сообщение неавторизованному пользователю с ссылкой для входа
     */
    protected function sendUnauthorizedMessage(TelegramMessageDto $message): void
    {
        $authLinkService = app(AuthLinkService::class);
        
        try {
            $authLink = $authLinkService->generateRegistrationLink([
                'telegram_id' => $message->userId,
            ], [
                'expires_in_minutes' => 60,
                'ip_address' => null,
                'user_agent' => 'Telegram Bot',
            ]);
            
            $text = "🔐 <b>Требуется авторизация</b>\n\n" .
                "Для использования этой команды необходимо войти в систему.\n\n" .
                "Нажмите на ссылку ниже для авторизации:\n" .
                "<a href=\"{$authLink['url']}\">Войти в систему</a>\n\n" .
                "Ссылка действительна 60 минут.";
                
            $this->reply($message, $text, TelegramService::FORMAT_HTML);
            
        } catch (\Exception $e) {
            $this->reply($message, "❌ Ошибка при создании ссылки для авторизации. Попробуйте позже.");
        }
    }

    /**
     * Создать ссылку для авторизации (для существующего пользователя)
     */
    protected function createAuthLink(User $user, TelegramMessageDto $message): array
    {
        $authLinkService = app(AuthLinkService::class);
        
        return $authLinkService->generateAuthLink($user, [
            'expires_in_minutes' => 15,
            'ip_address' => null,
            'user_agent' => 'Telegram Bot',
            'author_id' => $user->id,
        ]);
    }

    /**
     * Создать ссылку для регистрации (для нового пользователя)
     */
    protected function createRegistrationLink(TelegramMessageDto $message): array
    {
        $authLinkService = app(AuthLinkService::class);
        
        return $authLinkService->generateRegistrationLink([
            'telegram_id' => $message->userId,
        ], [
            'expires_in_minutes' => 60,
            'ip_address' => null,
            'user_agent' => 'Telegram Bot',
        ]);
    }

    /**
     * Обработать привязку аккаунта по токену из start_param
     */
    protected function handleAccountBinding(TelegramMessageDto $message): void
    {
        if (empty($message->arguments)) {
            return;
        }

        $startParam = $message->arguments[0] ?? null;
        
        if (empty($startParam)) {
            return;
        }

        $authLinkService = app(AuthLinkService::class);
        
        try {
            $result = $authLinkService->bindTelegramAccount($startParam, $message->userId, $message->botId);
            
            if ($result['success']) {
                $text = "✅ <b>Аккаунт успешно привязан!</b>\n\n" .
                    "Теперь вы можете использовать все функции бота.";
                $this->reply($message, $text, TelegramService::FORMAT_HTML);
            } else {
                $text = "❌ <b>Ошибка привязки аккаунта</b>\n\n" .
                    $result['message'] . "\n\n" .
                    "Попробуйте получить новую ссылку для авторизации.";
                $this->reply($message, $text, TelegramService::FORMAT_HTML);
            }
        } catch (\Exception $e) {
            $text = "❌ <b>Ошибка при привязке аккаунта</b>\n\n" .
                "Произошла техническая ошибка. Попробуйте позже.";
            $this->reply($message, $text, TelegramService::FORMAT_HTML);
        }
    }
}
