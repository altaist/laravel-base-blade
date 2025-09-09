<?php

namespace App\Enums;

enum ArticleStatus: string
{
    case DRAFT = 'draft';
    case READY_TO_PUBLISH = 'ready_to_publish';
    case PUBLISHED = 'published';

    public function getLabel(): string
    {
        return match($this) {
            self::DRAFT => 'Черновик',
            self::READY_TO_PUBLISH => 'Готова к публикации',
            self::PUBLISHED => 'Опубликована',
        };
    }

    public function getColor(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::READY_TO_PUBLISH => 'yellow',
            self::PUBLISHED => 'green',
        };
    }
}
