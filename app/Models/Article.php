<?php

namespace App\Models;

use App\Traits\Favoritable;
use App\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use Likeable, Favoritable;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'content',
        'seo_title',
        'seo_description',
        'seo_h1_title',
        'img_file_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
}
