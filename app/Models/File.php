<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
