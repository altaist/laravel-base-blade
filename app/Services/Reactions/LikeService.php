<?php

namespace App\Services\Reactions;

use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LikeService extends BaseReactionService
{
    protected function getReactionModel(): string
    {
        return Like::class;
    }
    
    protected function getTypeField(): string
    {
        return 'likeable_type';
    }
    
    protected function getIdField(): string
    {
        return 'likeable_id';
    }
    
    protected function getUserReactionsRelation(User $user): HasMany
    {
        return $user->likes()->with('likeable');
    }
    
    // Публичные методы с понятными названиями
    public function like(User $user, Model $likeable): Like
    {
        return $this->addReaction($user, $likeable);
    }
    
    public function unlike(User $user, Model $likeable): bool
    {
        return $this->removeReaction($user, $likeable);
    }
    
    public function toggleLike(User $user, Model $likeable): bool
    {
        return $this->toggleReaction($user, $likeable);
    }
    
    public function isLiked(User $user, Model $likeable): bool
    {
        return $this->hasReaction($user, $likeable);
    }
    
    public function getLikesCount(Model $likeable): int
    {
        return $this->getReactionsCount($likeable);
    }
    
    public function getUserLikes(User $user, ?string $type = null)
    {
        return $this->getUserReactions($user, $type);
    }
}
