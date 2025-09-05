<?php

namespace App\Helpers;

class FileHelper
{
    public static function generateKey(): string
    {
        return strtoupper(substr(md5(uniqid()), 0, 8));
    }

    public static function getFileExtension(string $filename): string
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    public static function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public static function isImage(string $mimeType): bool
    {
        return str_starts_with($mimeType, 'image/');
    }

    public static function validateFile(\Illuminate\Http\UploadedFile $file): array
    {
        $errors = [];
        
        if ($file->getSize() > 10 * 1024 * 1024) { // 10MB
            $errors[] = 'Файл слишком большой (максимум 10MB)';
        }
        
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp',
            'application/pdf', 'text/plain', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            $errors[] = 'Неподдерживаемый тип файла';
        }
        
        return $errors;
    }
}
