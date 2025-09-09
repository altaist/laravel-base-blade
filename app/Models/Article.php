<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use App\Traits\Favoritable;
use App\Traits\HasAttachments;
use App\Traits\Likeable;
use App\Traits\Statusable;
use App\Traits\HasContent;
use App\Traits\HasRichContent;
use App\Traits\HasSeo;
use App\Traits\HasMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Article extends Model
{
    use HasFactory, Likeable, Favoritable, HasAttachments, Statusable, 
        HasContent, HasRichContent, HasSeo, HasMedia;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'content',
        'rich_content',
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
        'rich_content' => 'array',
    ];


    /**
     * Связь с автором статьи
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с файлом изображения (дублируется в HasMedia, но оставляем для совместимости)
     */
    public function imgFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'img_file_id');
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
