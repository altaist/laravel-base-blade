<?php

namespace App\Services;

use App\Models\AuthLink;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuthLinkService
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Генерировать ссылку авторизации для существующего пользователя
     *
     * @param User $user
     * @param array $options
     * @return AuthLink
     */
    public function generateAuthLink(User $user, array $options = []): AuthLink
    {
        $defaultOptions = $this->getDefaultOptions(15);
        $options = array_merge($defaultOptions, $options);

        // Удаляем все предыдущие активные ссылки пользователя
        $this->deleteActiveLinks($user);

        return $user->authLinks()->create([
            'token' => Str::random(24), // Обычные токены - 24 символа
            'expires_at' => $this->calculateExpiryTime($options['expires_in_minutes']),
            'ip_address' => $options['ip_address'],
            'user_agent' => $options['user_agent'],
            'author_id' => $options['author_id'],
        ]);
    }

    /**
     * Генерировать ссылку для автоматической регистрации
     *
     * @param array $userData
     * @param array $options
     * @return AuthLink
     */
    public function generateRegistrationLink(array $userData, array $options = []): AuthLink
    {
        $defaultOptions = $this->getDefaultOptions(60); // Регистрационные ссылки живут дольше
        $options = array_merge($defaultOptions, $options);

        return AuthLink::create([
            'user_id' => null, // Нет привязки к пользователю
            'token' => Str::random(24), // Токены для регистрации - 24 символа
            'expires_at' => $this->calculateExpiryTime($options['expires_in_minutes']),
            'ip_address' => $options['ip_address'],
            'user_agent' => $options['user_agent'],
            'author_id' => $options['author_id'],
            ...$this->prepareUserData($userData),
        ]);
    }

    /**
     * Валидировать токен ссылки авторизации
     *
     * @param string $token
     * @return AuthLink|null
     */
    public function validateAuthLink(string $token): ?AuthLink
    {
        $authLink = AuthLink::where('token', $token)->first();
        return $authLink && $authLink->isActive() ? $authLink : null;
    }

    /**
     * Удалить ссылку после использования
     *
     * @param string $token
     * @return bool
     */
    public function deleteAfterUse(string $token): bool
    {
        $authLink = AuthLink::where('token', $token)->first();
        return $authLink ? $authLink->delete() : false;
    }

    /**
     * Удалить все активные ссылки пользователя
     *
     * @param User $user
     * @return int Количество удаленных ссылок
     */
    public function deleteActiveLinks(User $user): int
    {
        return $user->authLinks()->active()->delete();
    }

    /**
     * Очистить истекшие ссылки
     *
     * @return int Количество удаленных ссылок
     */
    public function cleanupExpiredLinks(): int
    {
        return AuthLink::expired()->delete();
    }

    /**
     * Получить активные ссылки пользователя
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveLinks(User $user)
    {
        return $user->authLinks()->active()->get();
    }

    /**
     * Получить статистику по ссылкам пользователя
     *
     * @param User $user
     * @return array
     */
    public function getLinksStats(User $user): array
    {
        return [
            'active' => $user->authLinks()->active()->count(),
            'total' => $user->authLinks()->withTrashed()->count(),
        ];
    }

    /**
     * Генерировать ссылку для привязки Telegram аккаунта
     *
     * @param User $user
     * @param array $options
     * @return AuthLink
     */
    public function generateTelegramBindingLink(User $user, array $options = []): AuthLink
    {
        $defaultOptions = $this->getDefaultOptions(60);
        $options = array_merge($defaultOptions, $options);
        
        return AuthLink::create([
            'user_id' => $user->id,
            'token' => 'tg_' . Str::random(16), // Префикс + 16 символов
            'expires_at' => $this->calculateExpiryTime($options['expires_in_minutes']),
            'ip_address' => $options['ip_address'],
            'user_agent' => $options['user_agent'],
            'author_id' => $options['author_id'],
        ]);
    }

    /**
     * Создать текстовую ссылку для Telegram
     *
     * @param User $user
     * @param array $options
     * @return string
     */
    public function createTelegramLink(User $user, array $options = []): string
    {
        $authLink = $this->generateTelegramBindingLink($user, $options);
        $botName = config('telegram.bot.name');
        
        if (!$botName) {
            throw new \Exception('TELEGRAM_BOT_NAME не настроен в .env файле');
        }
        
        return "https://t.me/{$botName}?start={$authLink->token}";
    }

    // ===== HELPER МЕТОДЫ =====

    /**
     * Получить опции по умолчанию
     */
    private function getDefaultOptions(int $expiresInMinutes): array
    {
        return [
            'expires_in_minutes' => $expiresInMinutes,
            'ip_address' => null,
            'user_agent' => null,
            'author_id' => null,
        ];
    }

    /**
     * Подготовить данные пользователя для создания ссылки
     */
    private function prepareUserData(array $userData): array
    {
        return [
            'name' => $userData['name'] ?? null,
            'email' => $userData['email'] ?? null,
            'role' => $userData['role'] ?? 'user',
            'telegram_id' => $userData['telegram_id'] ?? null,
            'telegram_username' => $userData['telegram_username'] ?? null,
        ];
    }

    /**
     * Рассчитать время истечения ссылки
     */
    private function calculateExpiryTime(int $minutes): Carbon
    {
        return Carbon::now()->addMinutes($minutes);
    }
}
