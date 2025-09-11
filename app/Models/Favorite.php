<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'favoritable_type',
        'favoritable_id',
    ];

    /**
     * Связь с пользователем
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Морфная связь с добавляемой в избранное сущностью
     */
    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }
}
