<?php

namespace App\Services;

use App\Models\AuthLink;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        $defaultOptions = [
            'expires_in_minutes' => 15,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        $options = array_merge($defaultOptions, $options);

        // Удаляем все предыдущие активные ссылки пользователя
        $this->deleteActiveLinks($user);

        return $user->authLinks()->create([
            'expires_at' => Carbon::now()->addMinutes($options['expires_in_minutes']),
            'ip_address' => $options['ip_address'],
            'user_agent' => $options['user_agent'],
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
        $defaultOptions = [
            'expires_in_minutes' => 60, // Регистрационные ссылки живут дольше
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        $options = array_merge($defaultOptions, $options);

        return AuthLink::create([
            'user_id' => null, // Нет привязки к пользователю
            'expires_at' => Carbon::now()->addMinutes($options['expires_in_minutes']),
            'ip_address' => $options['ip_address'],
            'user_agent' => $options['user_agent'],
            'name' => $userData['name'] ?? null,
            'email' => $userData['email'] ?? null,
            'role' => $userData['role'] ?? 'user',
            'telegram_id' => $userData['telegram_id'] ?? null,
            'telegram_username' => $userData['telegram_username'] ?? null,
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

        if (!$authLink || !$authLink->isActive()) {
            return null;
        }

        return $authLink;
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

        if (!$authLink) {
            return false;
        }

        return $authLink->delete();
    }

    /**
     * Удалить все активные ссылки пользователя
     *
     * @param User $user
     * @return int Количество удаленных ссылок
     */
    public function deleteActiveLinks(User $user): int
    {
        return $user->authLinks()
            ->active()
            ->delete();
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
}
