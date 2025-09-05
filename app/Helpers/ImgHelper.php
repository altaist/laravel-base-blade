<?php

namespace App\Helpers;

class ImgHelper
{
    public static function resize(string $path, int $width, int $height): void
    {
        // Простая оптимизация через GD (встроенная в PHP)
        if (!extension_loaded('gd')) {
            return;
        }

        $imageInfo = getimagesize($path);
        if (!$imageInfo) {
            return;
        }

        $mimeType = $imageInfo['mime'];
        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];

        // Создаем изображение из файла
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $source = imagecreatefrompng($path);
                break;
            case 'image/gif':
                $source = imagecreatefromgif($path);
                break;
            default:
                return;
        }

        if (!$source) {
            return;
        }

        // Создаем новое изображение с нужными размерами
        $resized = imagecreatetruecolor($width, $height);
        
        // Сохраняем прозрачность для PNG
        if ($mimeType === 'image/png') {
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefill($resized, 0, 0, $transparent);
        }

        // Изменяем размер
        imagecopyresampled($resized, $source, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

        // Сохраняем
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($resized, $path, 85);
                break;
            case 'image/png':
                imagepng($resized, $path, 8);
                break;
            case 'image/gif':
                imagegif($resized, $path);
                break;
        }

        // Освобождаем память
        imagedestroy($source);
        imagedestroy($resized);
    }

    public static function optimize(string $path): void
    {
        // Простая оптимизация через GD
        if (!extension_loaded('gd')) {
            return;
        }

        $imageInfo = getimagesize($path);
        if (!$imageInfo) {
            return;
        }

        $mimeType = $imageInfo['mime'];

        // Создаем изображение из файла
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($path);
                if ($source) {
                    imagejpeg($source, $path, 85); // Качество 85%
                    imagedestroy($source);
                }
                break;
            case 'image/png':
                $source = imagecreatefrompng($path);
                if ($source) {
                    imagepng($source, $path, 8); // Сжатие 8
                    imagedestroy($source);
                }
                break;
        }
    }
}
