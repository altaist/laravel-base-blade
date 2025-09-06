<?php

namespace App\Services\Referral;

use App\Models\Referral\Referral;
use App\Models\Referral\ReferralLink;
use App\Models\User;
use App\Enums\Referral\ReferralLinkType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ReferralService
{
    /**
     * Создать реферальную ссылку для пользователя
     */
    public function createLinkForUser(User $user, array $options = []): ReferralLink
    {
        $defaultOptions = [
            'name' => null,
            'type' => ReferralLinkType::CUSTOM,
            'max_uses' => null,
            'expires_at' => null,
        ];

        $options = array_merge($defaultOptions, $options);

        return ReferralLink::create([
            'user_id' => $user->id,
            'code' => ReferralLink::generateUniqueCode(),
            'name' => $options['name'],
            'type' => $options['type']->value,
            'max_uses' => $options['max_uses'],
            'expires_at' => $options['expires_at'],
        ]);
    }

    /**
     * Обработка перехода по реферальной ссылке
     */
    public function handleReferralClick(string $code): array
    {
        try {
            $referralLink = ReferralLink::where('code', $code)->first();

            if (!$referralLink) {
                return ['success' => false, 'message' => 'Реферальная ссылка не найдена'];
            }

            if (!$referralLink->isActive()) {
                return ['success' => false, 'message' => 'Реферальная ссылка неактивна или истекла'];
            }

            // Генерируем уникальные идентификаторы
            $cookieId = $this->generateCookieId();
            $fingerprint = $this->generateFingerprint();

            // Создаем запись о переходе
            $referral = Referral::create([
                'referral_link_id' => $referralLink->id,
                'referrer_id' => $referralLink->user_id,
                'visitor_cookie_id' => $cookieId,
                'visitor_fingerprint' => $fingerprint,
                'visitor_ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'pending',
                'expires_at' => now()->addMonth(), // Действует месяц
                'metadata' => [
                    'clicked_at' => now()->toISOString(),
                    'referrer' => now()->toISOString(),
                ],
            ]);

            // Устанавливаем cookie на месяц
            cookie()->queue('referral_tracking', $cookieId, 60 * 24 * 30); // 30 дней

            Log::info('Реферальный переход зарегистрирован', [
                'referral_link_id' => $referralLink->id,
                'referrer_id' => $referralLink->user_id,
                'cookie_id' => $cookieId,
                'ip' => request()->ip(),
            ]);

            return [
                'success' => true,
                'referral' => $referral,
                'referral_link' => $referralLink,
            ];

        } catch (\Exception $e) {
            Log::error('Ошибка при обработке реферального перехода', [
                'code' => $code,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ['success' => false, 'message' => 'Произошла ошибка при обработке ссылки'];
        }
    }

    /**
     * Обработка регистрации пользователя по реферальной ссылке
     */
    public function processReferralRegistration(User $newUser): bool
    {
        try {
            $cookieId = request()->cookie('referral_tracking');
            $fingerprint = $this->generateFingerprint();

            $referral = null;

            // Ищем сначала по cookie, потом по fingerprint
            if ($cookieId) {
                $referral = Referral::byCookieId($cookieId)
                    ->pending()
                    ->first();
            }

            if (!$referral && $fingerprint) {
                $referral = Referral::byFingerprint($fingerprint)
                    ->pending()
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            if (!$referral) {
                Log::info('Реферал не найден для регистрации', [
                    'user_id' => $newUser->id,
                    'cookie_id' => $cookieId,
                    'fingerprint' => $fingerprint,
                ]);
                return false;
            }

            // Проверяем, что реферал еще активен
            if (!$referral->isActive()) {
                Log::info('Реферал истек для регистрации', [
                    'referral_id' => $referral->id,
                    'user_id' => $newUser->id,
                ]);
                return false;
            }

            // Отмечаем реферал как завершенный
            $referral->markAsCompleted($newUser);

            // Увеличиваем счетчик использований ссылки
            $referral->referralLink->incrementUses();

            // Удаляем cookie
            cookie()->queue(cookie()->forget('referral_tracking'));

            Log::info('Реферал успешно завершен', [
                'referral_id' => $referral->id,
                'referrer_id' => $referral->referrer_id,
                'referred_id' => $newUser->id,
                'referral_link_id' => $referral->referral_link_id,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Ошибка при обработке реферальной регистрации', [
                'user_id' => $newUser->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Получить статистику рефералов пользователя
     */
    public function getUserReferralStats(User $user): array
    {
        $referralLinks = ReferralLink::byUser($user->id)->get();

        $totalStats = [
            'total_links' => $referralLinks->count(),
            'active_links' => $referralLinks->where('is_active', true)->count(),
            'total_clicks' => 0,
            'total_registrations' => 0,
            'total_pending' => 0,
            'overall_conversion_rate' => 0,
        ];

        foreach ($referralLinks as $link) {
            $stats = $link->stats;
            $totalStats['total_clicks'] += $stats['total_clicks'];
            $totalStats['total_registrations'] += $stats['completed_registrations'];
            $totalStats['total_pending'] += $stats['pending_registrations'];
        }

        if ($totalStats['total_clicks'] > 0) {
            $totalStats['overall_conversion_rate'] = round(
                ($totalStats['total_registrations'] / $totalStats['total_clicks']) * 100,
                2
            );
        }

        return $totalStats;
    }

    /**
     * Получить список реферальных ссылок пользователя
     */
    public function getUserLinks(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return ReferralLink::byUser($user->id)
            ->with(['referrals' => function ($query) {
                $query->select('referral_link_id', 'status')
                    ->selectRaw('COUNT(*) as count')
                    ->groupBy('referral_link_id', 'status');
            }])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Получить список приглашенных пользователей
     */
    public function getReferredUsers(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Referral::byReferrer($user->id)
            ->completed()
            ->with('referred')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Деактивировать реферальную ссылку
     */
    public function deactivateLink(ReferralLink $link): bool
    {
        try {
            $link->deactivate();
            return true;
        } catch (\Exception $e) {
            Log::error('Ошибка при деактивации реферальной ссылки', [
                'link_id' => $link->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Активировать реферальную ссылку
     */
    public function activateLink(ReferralLink $link): bool
    {
        try {
            $link->activate();
            return true;
        } catch (\Exception $e) {
            Log::error('Ошибка при активации реферальной ссылки', [
                'link_id' => $link->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Обновить настройки реферальной ссылки
     */
    public function updateLinkSettings(ReferralLink $link, array $settings): bool
    {
        try {
            $allowedFields = ['name', 'type', 'max_uses', 'expires_at'];
            $updateData = array_intersect_key($settings, array_flip($allowedFields));

            if (!empty($updateData)) {
                $link->update($updateData);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Ошибка при обновлении настроек реферальной ссылки', [
                'link_id' => $link->id,
                'settings' => $settings,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Очистить истекшие рефералы
     */
    public function cleanupExpiredReferrals(): int
    {
        return Referral::expired()
            ->where('status', 'pending')
            ->update(['status' => 'expired']);
    }

    /**
     * Генерировать уникальный cookie ID
     */
    private function generateCookieId(): string
    {
        return 'ref_' . Str::random(32);
    }

    /**
     * Генерировать fingerprint посетителя
     */
    private function generateFingerprint(): string
    {
        $data = [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'accept_language' => request()->header('Accept-Language'),
            'accept_encoding' => request()->header('Accept-Encoding'),
        ];

        return hash('sha256', json_encode($data));
    }
}
