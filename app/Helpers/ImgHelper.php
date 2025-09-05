<?php

namespace App\Helpers;

use Intervention\Image\Laravel\Facades\Image;

class ImgHelper
{
    public static function resize(string $path, int $width, int $height): void
    {
        Image::read($path)
            ->resize($width, $height)
            ->save($path);
    }

    public static function optimize(string $path): void
    {
        $image = Image::read($path);
        
        if ($image->origin()->mime() === 'image/jpeg') {
            $image->toJpeg(85);
        } elseif ($image->origin()->mime() === 'image/png') {
            $image->toPng(8);
        }
        
        $image->save($path);
    }
}
