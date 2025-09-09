<?php

namespace App\Services\Content;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;

class MediaService
{
    /**
     * Поддерживаемые типы изображений
     */
    private array $supportedImageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    /**
     * Размеры для миниатюр
     */
    private array $thumbnailSizes = [
        'small' => ['width' => 150, 'height' => 150],
        'medium' => ['width' => 300, 'height' => 300],
        'large' => ['width' => 600, 'height' => 600],
    ];

    /**
     * Размеры для социальных сетей
     */
    private array $socialSizes = [
        'og' => ['width' => 1200, 'height' => 630], // Open Graph
        'twitter' => ['width' => 1200, 'height' => 675], // Twitter Card
    ];

    /**
     * Получить URL изображения
     */
    public function getImageUrl(?File $file): ?string
    {
        if (!$file) {
            return null;
        }

        return $file->public_url ?? null;
    }

    /**
     * Получить альтернативный текст для изображения
     */
    public function getImageAlt(?File $file, ?Model $model = null): string
    {
        if (!$file) {
            return '';
        }

        // Пытаемся получить alt из метаданных файла
        if (!empty($file->alt_text)) {
            return $file->alt_text;
        }

        // Если есть модель, пытаемся получить alt из неё
        if ($model) {
            $altFields = ['seo_h1_title', 'name', 'title'];
            foreach ($altFields as $field) {
                if (isset($model->$field) && !empty($model->$field)) {
                    return $model->$field;
                }
            }
        }

        // Используем оригинальное имя файла
        return $file->original_name ?: 'Изображение';
    }

    /**
     * Проверить, является ли файл изображением
     */
    public function isImage(?File $file): bool
    {
        if (!$file) {
            return false;
        }

        $extension = strtolower($file->extension);
        return in_array($extension, $this->supportedImageTypes);
    }

    /**
     * Валидировать файл
     */
    public function validateFile(File $file, ?Model $model = null): bool
    {
        // Проверяем тип файла
        if (!$this->isImage($file)) {
            return false;
        }

        // Дополнительные проверки можно добавить здесь
        return true;
    }

    /**
     * Получить данные изображения для HTML
     */
    public function getImageData(?File $file, ?Model $model = null): array
    {
        if (!$file) {
            return [
                'url' => null,
                'alt' => '',
                'title' => '',
                'width' => null,
                'height' => null,
            ];
        }

        return [
            'url' => $this->getImageUrl($file),
            'alt' => $this->getImageAlt($file, $model),
            'title' => $file->original_name ?: '',
            'width' => $file->width,
            'height' => $file->height,
        ];
    }

    /**
     * Получить URL миниатюры
     */
    public function getThumbnailUrl(?File $file, string $size = 'small'): ?string
    {
        if (!$file || !$this->isImage($file)) {
            return null;
        }

        // Если размер не поддерживается, используем оригинал
        if (!isset($this->thumbnailSizes[$size])) {
            return $this->getImageUrl($file);
        }

        // Здесь можно добавить логику генерации миниатюр
        // Пока возвращаем оригинальный URL
        return $this->getImageUrl($file);
    }

    /**
     * Получить URL для социальных сетей
     */
    public function getSocialImageUrl(?File $file, string $platform = 'og'): ?string
    {
        if (!$file || !$this->isImage($file)) {
            return null;
        }

        // Если платформа не поддерживается, используем оригинал
        if (!isset($this->socialSizes[$platform])) {
            return $this->getImageUrl($file);
        }

        // Здесь можно добавить логику генерации изображений для соцсетей
        // Пока возвращаем оригинальный URL
        return $this->getImageUrl($file);
    }

    /**
     * Получить метаданные изображения
     */
    public function getImageMetadata(?File $file): array
    {
        if (!$file || !$this->isImage($file)) {
            return [];
        }

        return [
            'filename' => $file->original_name,
            'extension' => $file->extension,
            'mime_type' => $file->mime_type,
            'size' => $file->size,
            'width' => $file->width,
            'height' => $file->height,
            'alt_text' => $file->alt_text,
            'created_at' => $file->created_at,
        ];
    }

    /**
     * Оптимизировать изображение
     */
    public function optimizeImage(File $file, array $options = []): bool
    {
        if (!$this->isImage($file)) {
            return false;
        }

        // Здесь можно добавить логику оптимизации изображений
        // Например, сжатие, изменение размера и т.д.
        
        return true;
    }

    /**
     * Создать миниатюру
     */
    public function createThumbnail(File $file, string $size = 'small'): ?string
    {
        if (!$this->isImage($file) || !isset($this->thumbnailSizes[$size])) {
            return null;
        }

        // Здесь можно добавить логику создания миниатюр
        // Пока возвращаем оригинальный URL
        return $this->getImageUrl($file);
    }

    /**
     * Получить HTML тег изображения
     */
    public function getImageTag(?File $file, ?Model $model = null, array $attributes = []): string
    {
        if (!$file) {
            return '';
        }

        $data = $this->getImageData($file, $model);
        
        $defaultAttributes = [
            'src' => $data['url'],
            'alt' => $data['alt'],
            'title' => $data['title'],
        ];

        // Добавляем размеры если они есть
        if ($data['width']) {
            $defaultAttributes['width'] = $data['width'];
        }
        if ($data['height']) {
            $defaultAttributes['height'] = $data['height'];
        }

        // Объединяем с переданными атрибутами
        $attributes = array_merge($defaultAttributes, $attributes);

        // Формируем атрибуты
        $attrString = '';
        foreach ($attributes as $key => $value) {
            if ($value !== null) {
                $attrString .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }

        return '<img' . $attrString . '>';
    }

    /**
     * Получить CSS классы для изображения
     */
    public function getImageClasses(?File $file, string $context = 'default'): string
    {
        if (!$file) {
            return '';
        }

        $classes = ['img-fluid']; // Bootstrap класс по умолчанию

        // Добавляем контекстные классы
        switch ($context) {
            case 'thumbnail':
                $classes[] = 'img-thumbnail';
                break;
            case 'rounded':
                $classes[] = 'rounded';
                break;
            case 'circle':
                $classes[] = 'rounded-circle';
                break;
        }

        return implode(' ', $classes);
    }

    /**
     * Проверить, поддерживается ли тип файла
     */
    public function isSupportedImageType(string $extension): bool
    {
        return in_array(strtolower($extension), $this->supportedImageTypes);
    }

    /**
     * Получить поддерживаемые типы изображений
     */
    public function getSupportedImageTypes(): array
    {
        return $this->supportedImageTypes;
    }

    /**
     * Получить размеры миниатюр
     */
    public function getThumbnailSizes(): array
    {
        return $this->thumbnailSizes;
    }

    /**
     * Получить размеры для социальных сетей
     */
    public function getSocialSizes(): array
    {
        return $this->socialSizes;
    }

    /**
     * Получить рекомендуемые размеры для изображения
     */
    public function getRecommendedSizes(string $context = 'default'): array
    {
        $recommendations = [
            'default' => ['width' => 800, 'height' => 600],
            'hero' => ['width' => 1920, 'height' => 1080],
            'card' => ['width' => 400, 'height' => 300],
            'avatar' => ['width' => 200, 'height' => 200],
        ];

        return $recommendations[$context] ?? $recommendations['default'];
    }
}
