<?php

namespace App\Traits;

use App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

trait HasAttachments
{
    /**
     * Связь с attachments (морфная)
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'related');
    }
    
    /**
     * Получить все изображения из attachments
     */
    public function getImages(): Collection
    {
        return $this->attachments()
            ->where('type', 'image')
            ->with('file')
            ->get()
            ->pluck('file')
            ->filter();
    }
    
    /**
     * Получить все документы из attachments
     */
    public function getDocuments(): Collection
    {
        return $this->attachments()
            ->where('type', 'document')
            ->with('file')
            ->get()
            ->pluck('file')
            ->filter();
    }
    
    /**
     * Получить attachments по типу
     */
    public function getAttachmentsByType(string $type): Collection
    {
        return $this->attachments()
            ->where('type', $type)
            ->with('file')
            ->get();
    }
    
    /**
     * Получить все файлы (изображения + документы)
     */
    public function getAllFiles(): Collection
    {
        return $this->attachments()
            ->with('file')
            ->get()
            ->pluck('file')
            ->filter();
    }
    
    /**
     * Получить URL всех изображений для Blade
     */
    public function getImageUrls(): array
    {
        return $this->getImages()
            ->map(function ($file) {
                return $file->public_url;
            })
            ->filter()
            ->values()
            ->toArray();
    }
    
    /**
     * Получить URL всех документов для Blade
     */
    public function getDocumentUrls(): array
    {
        return $this->getDocuments()
            ->map(function ($file) {
                return $file->public_url;
            })
            ->filter()
            ->values()
            ->toArray();
    }
    
    /**
     * Получить URL attachments по типу для Blade
     */
    public function getAttachmentUrlsByType(string $type): array
    {
        return $this->getAttachmentsByType($type)
            ->map(function ($attachment) {
                return $attachment->file->public_url ?? null;
            })
            ->filter()
            ->values()
            ->toArray();
    }
    
    /**
     * Получить количество изображений
     */
    public function getImagesCount(): int
    {
        return $this->attachments()->where('type', 'image')->count();
    }
    
    /**
     * Получить количество документов
     */
    public function getDocumentsCount(): int
    {
        return $this->attachments()->where('type', 'document')->count();
    }
    
    /**
     * Проверить, есть ли изображения
     */
    public function hasImages(): bool
    {
        return $this->getImagesCount() > 0;
    }
    
    /**
     * Проверить, есть ли документы
     */
    public function hasDocuments(): bool
    {
        return $this->getDocumentsCount() > 0;
    }
}
