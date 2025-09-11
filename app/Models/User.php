<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telegram_id',
        'telegram_username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function person(): HasOne
    {
        return $this->hasOne(Person::class);
    }

    /**
     * Связь с ссылками авторизации
     */
    public function authLinks(): HasMany
    {
        return $this->hasMany(AuthLink::class);
    }

    /**
     * Связь с attachments (морф)
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related');
    }

    /**
     * Связь с лайками пользователя
     */
    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Связь с избранным пользователя
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Связь с файлами
     */
    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    /**
     * Связь с реферальными ссылками пользователя
     */
    public function referralLinks(): HasMany
    {
        return $this->hasMany(\App\Models\Referral\ReferralLink::class);
    }

    /**
     * Связь с рефералами, где пользователь является реферером
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(\App\Models\Referral\Referral::class, 'referrer_id');
    }

    /**
     * Связь с рефералом, где пользователь был приглашен
     */
    public function referredBy(): HasOne
    {
        return $this->hasOne(\App\Models\Referral\Referral::class, 'referred_id');
    }

    /**
     * Получить изображения пользователя
     */
    public function getImagesAttribute()
    {
        return $this->attachments()->where('type', 'image')->get();
    }

    /**
     * Получить документы пользователя
     */
    public function getDocumentsAttribute()
    {
        return $this->attachments()->where('type', 'document')->get();
    }

    /**
     * Получить Telegram ID для уведомлений
     */
    public function routeNotificationForTelegram()
    {
        return $this->telegram_id;
    }
}
