<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    protected $fillable = [
        'original_name',
        'mime_type',
        'size',
        'extension',
        'disk',
        'path',
        'key',
        'is_public',
        'metadata',
        'user_id',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_public' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с attachments
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    /**
     * Проверить, используется ли файл в attachments
     */
    public function isUsedInAttachments(): bool
    {
        return $this->attachments()->exists();
    }

    /**
     * Получить количество attachments для файла
     */
    public function getAttachmentsCountAttribute(): int
    {
        return $this->attachments()->count();
    }

    /**
     * Проверить, является ли файл изображением
     */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Проверить, является ли файл документом
     */
    public function isDocument(): bool
    {
        return !$this->isImage();
    }

    public function getPublicUrlAttribute(): ?string
    {
        if (!$this->is_public || !$this->key) {
            return null;
        }
        
        return route('files.public.download', $this->key);
    }

    public function getFilenameAttribute(): string
    {
        return $this->id . '.' . $this->extension;
    }
}
