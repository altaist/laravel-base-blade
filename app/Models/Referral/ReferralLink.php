<?php

namespace App\Models\Referral;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ReferralLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'name',
        'type',
        'is_active',
        'max_uses',
        'current_uses',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Связь с пользователем-владельцем ссылки
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с рефералами по этой ссылке
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Скоуп для активных ссылок
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Скоуп для ссылок определенного пользователя
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Скоуп для ссылок определенного типа
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Проверка, активна ли ссылка
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses && $this->current_uses >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Проверка, истекла ли ссылка
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Проверка, достигнуто ли максимальное количество использований
     */
    public function isMaxUsesReached(): bool
    {
        return $this->max_uses && $this->current_uses >= $this->max_uses;
    }

    /**
     * Увеличить счетчик использований
     */
    public function incrementUses(): void
    {
        $this->increment('current_uses');
    }

    /**
     * Деактивировать ссылку
     */
    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Активировать ссылку
     */
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    /**
     * Генерировать уникальный код ссылки
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = Str::random(8);
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /**
     * Получить полный URL ссылки
     */
    public function getFullUrlAttribute(): string
    {
        return url("/ref/{$this->code}");
    }

    /**
     * Получить статистику по ссылке
     */
    public function getStatsAttribute(): array
    {
        return [
            'total_clicks' => $this->referrals()->count(),
            'completed_registrations' => $this->referrals()->where('status', 'completed')->count(),
            'pending_registrations' => $this->referrals()->where('status', 'pending')->count(),
            'conversion_rate' => $this->referrals()->count() > 0 
                ? round(($this->referrals()->where('status', 'completed')->count() / $this->referrals()->count()) * 100, 2)
                : 0,
        ];
    }
}
