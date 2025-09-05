<?php

namespace App\Enums;

enum AttachmentType: string
{
    case IMAGE = 'image';
    case DOCUMENT = 'document';

    public function getLabel(): string
    {
        return match($this) {
            self::IMAGE => 'Изображение',
            self::DOCUMENT => 'Документ',
        };
    }

    public static function getDefault(): self
    {
        return self::IMAGE;
    }
}
