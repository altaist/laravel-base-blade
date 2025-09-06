<?php

namespace App\Models\Referral;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_link_id',
        'referrer_id',
        'referred_id',
        'visitor_cookie_id',
        'visitor_fingerprint',
        'visitor_ip',
        'user_agent',
        'status',
        'expires_at',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Связь с реферальной ссылкой
     */
    public function referralLink(): BelongsTo
    {
        return $this->belongsTo(ReferralLink::class);
    }

    /**
     * Связь с пользователем-реферером
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    /**
     * Связь с приглашенным пользователем
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_id');
    }

    /**
     * Скоуп для ожидающих рефералов
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }

    /**
     * Скоуп для завершенных рефералов
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Скоуп для истекших рефералов
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Скоуп для рефералов определенного пользователя
     */
    public function scopeByReferrer($query, $referrerId)
    {
        return $query->where('referrer_id', $referrerId);
    }

    /**
     * Скоуп для рефералов по cookie ID
     */
    public function scopeByCookieId($query, $cookieId)
    {
        return $query->where('visitor_cookie_id', $cookieId);
    }

    /**
     * Скоуп для рефералов по fingerprint
     */
    public function scopeByFingerprint($query, $fingerprint)
    {
        return $query->where('visitor_fingerprint', $fingerprint);
    }

    /**
     * Проверка, активен ли реферал
     */
    public function isActive(): bool
    {
        return $this->status === 'pending' && $this->expires_at->isFuture();
    }

    /**
     * Проверка, истек ли реферал
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Проверка, завершен ли реферал
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Отметить как завершенный
     */
    public function markAsCompleted(User $referredUser): void
    {
        $this->update([
            'referred_id' => $referredUser->id,
            'status' => 'completed',
        ]);
    }

    /**
     * Отметить как истекший
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Получить время до истечения
     */
    public function getTimeUntilExpiryAttribute(): ?string
    {
        if ($this->expires_at->isPast()) {
            return null;
        }

        return $this->expires_at->diffForHumans();
    }

    /**
     * Получить информацию о посетителе
     */
    public function getVisitorInfoAttribute(): array
    {
        return [
            'ip' => $this->visitor_ip,
            'user_agent' => $this->user_agent,
            'cookie_id' => $this->visitor_cookie_id,
            'fingerprint' => $this->visitor_fingerprint,
        ];
    }
}
