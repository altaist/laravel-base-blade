<?php

namespace App\Traits;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Favoritable
{
    /**
     * Получить все записи избранного для этой модели
     */
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }
    
    /**
     * Проверить, добавил ли пользователь эту сущность в избранное
     */
    public function isFavoritedBy(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
    
    /**
     * Получить количество добавлений в избранное
     */
    public function favoritesCount(): int
    {
        return $this->favorites()->count();
    }
    
    /**
     * Получить запись избранного от конкретного пользователя
     */
    public function getFavoriteFromUser(User $user): ?Favorite
    {
        return $this->favorites()->where('user_id', $user->id)->first();
    }
}
