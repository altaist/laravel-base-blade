<?php

namespace App\Traits;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Likeable
{
    /**
     * Получить все лайки для этой модели
     */
    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
    
    /**
     * Проверить, лайкнул ли пользователь эту сущность
     */
    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
    
    /**
     * Получить количество лайков
     */
    public function likesCount(): int
    {
        return $this->likes()->count();
    }
    
    /**
     * Получить лайк от конкретного пользователя
     */
    public function getLikeFromUser(User $user): ?Like
    {
        return $this->likes()->where('user_id', $user->id)->first();
    }
}
