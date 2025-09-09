<?php

namespace App\Traits;

use App\Services\StatusService;
use Illuminate\Database\Eloquent\Model;

trait Statusable
{
    /**
     * Изменить статус модели
     */
    public function changeStatus(string $status): Model
    {
        return StatusService::changeStatusFor($this, $status);
    }
    
    /**
     * Опубликовать модель
     */
    public function publish(): Model
    {
        return StatusService::publishFor($this);
    }
    
    /**
     * Снять с публикации
     */
    public function unpublish(): Model
    {
        return StatusService::unpublishFor($this);
    }
    
    /**
     * Отметить как готовую к публикации
     */
    public function markAsReady(): Model
    {
        return StatusService::markAsReadyFor($this);
    }
    
    /**
     * Отметить как черновик
     */
    public function markAsDraft(): Model
    {
        return StatusService::markAsDraftFor($this);
    }
    
    /**
     * Проверить, можно ли изменить статус
     */
    public function canChangeStatusTo(string $newStatus): bool
    {
        return StatusService::canChangeStatusFor($this, $newStatus);
    }
    
    /**
     * Получить доступные статусы
     */
    public function getAvailableStatuses(): array
    {
        return StatusService::getAvailableStatusesFor($this);
    }
    
}
