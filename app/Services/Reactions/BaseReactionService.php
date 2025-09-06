<?php

namespace App\Services\Reactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

abstract class BaseReactionService
{
    /**
     * Получить модель реакции (Like, Favorite и т.д.)
     */
    abstract protected function getReactionModel(): string;
    
    /**
     * Получить название поля типа (likeable_type, favoritable_type и т.д.)
     */
    abstract protected function getTypeField(): string;
    
    /**
     * Получить название поля ID (likeable_id, favoritable_id и т.д.)
     */
    abstract protected function getIdField(): string;
    
    /**
     * Получить связь пользователя с реакциями
     */
    abstract protected function getUserReactionsRelation(User $user): HasMany;
    
    /**
     * Добавить реакцию
     */
    public function addReaction(User $user, Model $reactable): Model
    {
        return DB::transaction(function () use ($user, $reactable) {
            $model = $this->getReactionModel();
            
            // Проверяем, не добавил ли уже пользователь реакцию
            $existingReaction = $model::where([
                'user_id' => $user->id,
                $this->getTypeField() => get_class($reactable),
                $this->getIdField() => $reactable->id,
            ])->first();

            if ($existingReaction) {
                return $existingReaction;
            }

            // Создаем новую реакцию
            return $model::create([
                'user_id' => $user->id,
                $this->getTypeField() => get_class($reactable),
                $this->getIdField() => $reactable->id,
            ]);
        });
    }
    
    /**
     * Убрать реакцию
     */
    public function removeReaction(User $user, Model $reactable): bool
    {
        return DB::transaction(function () use ($user, $reactable) {
            $model = $this->getReactionModel();
            
            $deleted = $model::where([
                'user_id' => $user->id,
                $this->getTypeField() => get_class($reactable),
                $this->getIdField() => $reactable->id,
            ])->delete();
            
            return $deleted > 0;
        });
    }
    
    /**
     * Переключить реакцию (если есть - убрать, если нет - добавить)
     */
    public function toggleReaction(User $user, Model $reactable): bool
    {
        if ($this->hasReaction($user, $reactable)) {
            $this->removeReaction($user, $reactable);
            return false; // Убрали реакцию
        } else {
            $this->addReaction($user, $reactable);
            return true; // Добавили реакцию
        }
    }
    
    /**
     * Проверить, есть ли реакция от пользователя
     */
    public function hasReaction(User $user, Model $reactable): bool
    {
        $model = $this->getReactionModel();
        
        return $model::where([
            'user_id' => $user->id,
            $this->getTypeField() => get_class($reactable),
            $this->getIdField() => $reactable->id,
        ])->exists();
    }
    
    /**
     * Получить количество реакций для сущности
     */
    public function getReactionsCount(Model $reactable): int
    {
        $model = $this->getReactionModel();
        
        return $model::where([
            $this->getTypeField() => get_class($reactable),
            $this->getIdField() => $reactable->id,
        ])->count();
    }
    
    /**
     * Получить все реакции пользователя
     */
    public function getUserReactions(User $user, ?string $type = null)
    {
        $query = $this->getUserReactionsRelation($user);
        
        if ($type) {
            $query->where($this->getTypeField(), $type);
        }
        
        return $query->get();
    }
}
