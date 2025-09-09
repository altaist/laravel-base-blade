<?php

namespace App\Services;

use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class StatusService
{
    // Статические методы для работы с любой моделью
    public static function changeStatusFor(Model $model, string $status): Model
    {
        if (!self::canChangeStatusFor($model, $status)) {
            throw new \InvalidArgumentException("Cannot change status to {$status}");
        }
        
        $model->update(['status' => $status]);
        return $model->fresh();
    }
    
    public static function publishFor(Model $model): Model
    {
        return self::changeStatusFor($model, ArticleStatus::PUBLISHED->value);
    }
    
    public static function unpublishFor(Model $model): Model
    {
        return self::changeStatusFor($model, ArticleStatus::DRAFT->value);
    }
    
    public static function markAsReadyFor(Model $model): Model
    {
        return self::changeStatusFor($model, ArticleStatus::READY_TO_PUBLISH->value);
    }
    
    public static function markAsDraftFor(Model $model): Model
    {
        return self::changeStatusFor($model, ArticleStatus::DRAFT->value);
    }
    
    public static function canChangeStatusFor(Model $model, string $newStatus): bool
    {
        // Проверяем, что модель имеет поле status
        if (!$model->getConnection()->getSchemaBuilder()->hasColumn($model->getTable(), 'status')) {
            return false;
        }
        
        // Проверяем, что новый статус валидный
        $availableStatuses = self::getAvailableStatusesFor($model);
        return in_array($newStatus, $availableStatuses);
    }
    
    public static function getAvailableStatusesFor(Model $model): array
    {
        // Определяем enum по типу модели
        if ($model instanceof \App\Models\Article) {
            return array_map(fn($case) => $case->value, ArticleStatus::cases());
        }
        
        // Для других моделей можно добавить логику
        return [];
    }
    
    // Массовые операции
    public static function bulkChangeStatus(Collection $models, string $status): Collection
    {
        return $models->map(function ($model) use ($status) {
            return self::changeStatusFor($model, $status);
        });
    }
}
