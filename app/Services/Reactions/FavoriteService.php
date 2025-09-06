<?php

namespace App\Services\Reactions;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FavoriteService extends BaseReactionService
{
    protected function getReactionModel(): string
    {
        return Favorite::class;
    }
    
    protected function getTypeField(): string
    {
        return 'favoritable_type';
    }
    
    protected function getIdField(): string
    {
        return 'favoritable_id';
    }
    
    protected function getUserReactionsRelation(User $user): HasMany
    {
        return $user->favorites()->with('favoritable');
    }
    
    // Публичные методы с понятными названиями
    public function addToFavorites(User $user, Model $favoritable): Favorite
    {
        return $this->addReaction($user, $favoritable);
    }
    
    public function removeFromFavorites(User $user, Model $favoritable): bool
    {
        return $this->removeReaction($user, $favoritable);
    }
    
    public function toggleFavorite(User $user, Model $favoritable): bool
    {
        return $this->toggleReaction($user, $favoritable);
    }
    
    public function isFavorited(User $user, Model $favoritable): bool
    {
        return $this->hasReaction($user, $favoritable);
    }
    
    public function getFavoritesCount(Model $favoritable): int
    {
        return $this->getReactionsCount($favoritable);
    }
    
    public function getUserFavorites(User $user, ?string $type = null)
    {
        return $this->getUserReactions($user, $type);
    }
}
