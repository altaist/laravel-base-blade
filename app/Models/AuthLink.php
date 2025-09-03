<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class AuthLink extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
        'ip_address',
        'user_agent',
        'name',
        'email',
        'role',
        'telegram_id',
        'telegram_username',
        'author_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Связь с пользователем (может быть null для новых пользователей)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с автором ссылки
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Скоуп для активных (неистекших) ссылок
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Скоуп для истекших ссылок
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Скоуп для ссылок определенного автора
     */
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Проверка, активна ли ссылка
     */
    public function isActive(): bool
    {
        return $this->expires_at->isFuture();
    }

    /**
     * Проверка, истекла ли ссылка
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Проверка, предназначена ли ссылка для регистрации
     */
    public function isForRegistration(): bool
    {
        return $this->user_id === null && !empty($this->name);
    }

    /**
     * Boot метод для автоматической генерации токена
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($authLink) {
            if (empty($authLink->token)) {
                $authLink->token = Str::random(64);
            }
        });
    }
}
