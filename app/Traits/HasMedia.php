<?php

namespace App\Traits;

use App\Models\File;
use App\Services\Content\MediaService;

trait HasMedia
{
    /**
     * Поля медиа для модели
     */
    protected $mediaFields = [
        'img_file_id'
    ];

    /**
     * Связь с основным изображением
     */
    public function imgFile()
    {
        return $this->belongsTo(File::class, 'img_file_id');
    }

    /**
     * Получить основное изображение
     */
    public function getMainImage(): ?File
    {
        return $this->imgFile;
    }

    /**
     * Проверить, есть ли основное изображение
     */
    public function hasMainImage(): bool
    {
        return !is_null($this->img_file_id) && !is_null($this->imgFile);
    }

    /**
     * Получить URL основного изображения
     */
    public function getMainImageUrl(): ?string
    {
        if (!$this->hasMainImage()) {
            return null;
        }

        return app(MediaService::class)->getImageUrl($this->imgFile);
    }

    /**
     * Получить альтернативный текст для изображения
     */
    public function getMainImageAlt(): string
    {
        if (!$this->hasMainImage()) {
            return '';
        }

        return app(MediaService::class)->getImageAlt($this->imgFile, $this);
    }

    /**
     * Установить основное изображение
     */
    public function setMainImage(?int $fileId): void
    {
        if ($fileId) {
            // Проверяем, что файл существует и является изображением
            $file = File::find($fileId);
            if ($file && app(MediaService::class)->isImage($file)) {
                $this->update(['img_file_id' => $fileId]);
            }
        } else {
            $this->update(['img_file_id' => null]);
        }
    }

    /**
     * Удалить основное изображение
     */
    public function removeMainImage(): void
    {
        $this->update(['img_file_id' => null]);
    }

    /**
     * Получить данные изображения для HTML
     */
    public function getMainImageData(): array
    {
        if (!$this->hasMainImage()) {
            return [
                'url' => null,
                'alt' => '',
                'title' => '',
                'width' => null,
                'height' => null,
            ];
        }

        return app(MediaService::class)->getImageData($this->imgFile, $this);
    }

    /**
     * Получить изображение для списков (миниатюра)
     */
    public function getThumbnailUrl(string $size = 'small'): ?string
    {
        if (!$this->hasMainImage()) {
            return null;
        }

        return app(MediaService::class)->getThumbnailUrl($this->imgFile, $size);
    }

    /**
     * Получить изображение для социальных сетей (Open Graph)
     */
    public function getSocialImageUrl(): ?string
    {
        if (!$this->hasMainImage()) {
            return null;
        }

        return app(MediaService::class)->getSocialImageUrl($this->imgFile);
    }

    /**
     * Валидировать медиа файл
     */
    public function validateMediaFile(int $fileId): bool
    {
        $file = File::find($fileId);
        return $file && app(MediaService::class)->validateFile($file, $this);
    }

    /**
     * Получить все медиа поля
     */
    public function getAllMediaFields(): array
    {
        $media = [];
        
        foreach ($this->mediaFields as $field) {
            $media[$field] = $this->getAttribute($field);
        }
        
        return $media;
    }

    /**
     * Проверить, есть ли медиа файлы
     */
    public function hasMedia(): bool
    {
        foreach ($this->mediaFields as $field) {
            if (!empty($this->getAttribute($field))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Получить метаданные для изображения
     */
    public function getImageMetadata(): array
    {
        if (!$this->hasMainImage()) {
            return [];
        }

        return app(MediaService::class)->getImageMetadata($this->imgFile);
    }

    /**
     * Обновить медиа поля
     */
    public function updateMediaFields(array $mediaData): void
    {
        $updateData = [];
        
        foreach ($this->mediaFields as $field) {
            if (isset($mediaData[$field])) {
                if ($field === 'img_file_id' && $mediaData[$field]) {
                    // Валидируем файл перед установкой
                    if ($this->validateMediaFile($mediaData[$field])) {
                        $updateData[$field] = $mediaData[$field];
                    }
                } else {
                    $updateData[$field] = $mediaData[$field];
                }
            }
        }
        
        if (!empty($updateData)) {
            $this->update($updateData);
        }
    }
}
