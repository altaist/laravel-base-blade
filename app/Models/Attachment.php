<?php

namespace App\Models;

use App\Enums\AttachmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Attachment extends Model
{
    protected $fillable = [
        'related_type',
        'related_id',
        'file_id',
        'url',
        'name',
        'description',
        'type',
    ];

    protected $casts = [
        'type' => AttachmentType::class,
    ];

    /**
     * Получить связанную модель (morph)
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Получить связанный файл
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Получить владельца attachment через файл
     */
    public function getOwnerAttribute(): ?User
    {
        return $this->file?->user;
    }

    /**
     * Проверить, является ли attachment изображением
     */
    public function isImage(): bool
    {
        return $this->type === AttachmentType::IMAGE;
    }

    /**
     * Проверить, является ли attachment документом
     */
    public function isDocument(): bool
    {
        return $this->type === AttachmentType::DOCUMENT;
    }

    /**
     * Получить URL для отображения
     */
    public function getDisplayUrlAttribute(): ?string
    {
        if ($this->url) {
            return $this->url;
        }

        return $this->file?->public_url;
    }

    /**
     * Получить название для отображения
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?? $this->file?->original_name ?? 'Без названия';
    }
}
