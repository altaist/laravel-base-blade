<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use App\Traits\Favoritable;
use App\Traits\HasAttachments;
use App\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Article extends Model
{
    use Likeable, Favoritable, HasAttachments;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'content',
        'seo_title',
        'seo_description',
        'seo_h1_title',
        'status',
        'img_file_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status' => ArticleStatus::class,
    ];

    /**
     * Связь с автором статьи
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с файлом изображения
     */
    public function imgFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'img_file_id');
    }

    /**
     * Получить URL изображения для HTML
     */
    public function getImgUrlAttribute(): ?string
    {
        if (!$this->img_file_id || !$this->imgFile) {
            return null;
        }

        return $this->imgFile->public_url;
    }

    /**
     * Получить альтернативный текст для изображения
     */
    public function getImgAltAttribute(): string
    {
        return $this->seo_h1_title ?: $this->name;
    }


    /**
     * Проверить, является ли статья черновиком
     */
    public function isDraft(): bool
    {
        return $this->status === ArticleStatus::DRAFT;
    }

    /**
     * Проверить, готова ли статья к публикации
     */
    public function isReadyToPublish(): bool
    {
        return $this->status === ArticleStatus::READY_TO_PUBLISH;
    }

    /**
     * Проверить, опубликована ли статья
     */
    public function isPublished(): bool
    {
        return $this->status === ArticleStatus::PUBLISHED;
    }

}
