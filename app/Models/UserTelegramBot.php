<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTelegramBot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bot_name',
        'telegram_id',
        'bot_data',
    ];

    protected $casts = [
        'bot_data' => 'array',
    ];

    /**
     * Связь с пользователем
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Создать или обновить привязку пользователя к боту
     */
    public static function createOrUpdate(array $data): self
    {
        // Проверяем, не привязан ли этот telegram_id к другому пользователю
        $existing = self::where('telegram_id', $data['telegram_id'])
            ->where('user_id', '!=', $data['user_id'])
            ->first();
            
        if ($existing) {
            throw new \Exception("Telegram ID уже привязан к другому пользователю");
        }
        
        return self::updateOrCreate(
            ['user_id' => $data['user_id'], 'bot_name' => $data['bot_name']],
            $data
        );
    }
}