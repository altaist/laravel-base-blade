<?php

namespace App\Services\Content;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SeoService
{
    /**
     * Генерировать SEO заголовок
     */
    public function generateSeoTitle(Model $model): string
    {
        // Если есть явный SEO заголовок, используем его
        if (!empty($model->seo_title)) {
            return $model->seo_title;
        }

        // Пытаемся получить название из контента
        $title = $this->getTitleFromModel($model);
        
        if (empty($title)) {
            return config('app.name');
        }

        // Добавляем название сайта если заголовок короткий
        if (mb_strlen($title) < 40) {
            return $title . ' | ' . config('app.name');
        }

        return $title;
    }

    /**
     * Генерировать SEO описание
     */
    public function generateSeoDescription(Model $model): string
    {
        // Если есть явное SEO описание, используем его
        if (!empty($model->seo_description)) {
            return $model->seo_description;
        }

        // Пытаемся получить описание из контента
        $description = $this->getDescriptionFromModel($model);
        
        if (empty($description)) {
            return $this->getDefaultSeoDescription();
        }

        // Ограничиваем длину для SEO (160 символов)
        return Str::limit($description, 160);
    }

    /**
     * Генерировать H1 заголовок
     */
    public function generateH1Title(Model $model): string
    {
        // Если есть явный H1 заголовок, используем его
        if (!empty($model->seo_h1_title)) {
            return $model->seo_h1_title;
        }

        // Пытаемся получить название из контента
        $title = $this->getTitleFromModel($model);
        
        return $title ?: 'Заголовок';
    }

    /**
     * Генерировать slug
     */
    public function generateSlug(string $name, ?int $excludeId = null, ?string $modelClass = null): string
    {
        if (empty($name)) {
            return 'untitled-' . uniqid();
        }

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        // Определяем модель для проверки уникальности
        $model = $modelClass ?: $this->getDefaultModelClass();

        while ($this->slugExists($slug, $excludeId, $model)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Проверить уникальность slug
     */
    public function isSlugUnique(string $slug, ?int $excludeId, string $modelClass): bool
    {
        return !$this->slugExists($slug, $excludeId, $modelClass);
    }

    /**
     * Автоматически генерировать все SEO поля из контента
     */
    public function generateSeoFromContent(Model $model): array
    {
        return [
            'seo_title' => $this->generateSeoTitle($model),
            'seo_description' => $this->generateSeoDescription($model),
            'seo_h1_title' => $this->generateH1Title($model),
        ];
    }

    /**
     * Валидировать SEO данные
     */
    public function validateSeoData(array $data): array
    {
        $errors = [];

        // Валидация SEO заголовка
        if (isset($data['seo_title'])) {
            $title = $data['seo_title'];
            if (mb_strlen($title) > 60) {
                $errors['seo_title'] = 'SEO заголовок не должен превышать 60 символов';
            }
            if (mb_strlen($title) < 10) {
                $errors['seo_title'] = 'SEO заголовок должен содержать минимум 10 символов';
            }
        }

        // Валидация SEO описания
        if (isset($data['seo_description'])) {
            $description = $data['seo_description'];
            if (mb_strlen($description) > 160) {
                $errors['seo_description'] = 'SEO описание не должно превышать 160 символов';
            }
            if (mb_strlen($description) < 20) {
                $errors['seo_description'] = 'SEO описание должно содержать минимум 20 символов';
            }
        }

        // Валидация H1 заголовка
        if (isset($data['seo_h1_title'])) {
            $h1 = $data['seo_h1_title'];
            if (mb_strlen($h1) > 100) {
                $errors['seo_h1_title'] = 'H1 заголовок не должен превышать 100 символов';
            }
            if (mb_strlen($h1) < 5) {
                $errors['seo_h1_title'] = 'H1 заголовок должен содержать минимум 5 символов';
            }
        }

        // Валидация slug
        if (isset($data['slug'])) {
            $slug = $data['slug'];
            if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
                $errors['slug'] = 'Slug может содержать только строчные буквы, цифры и дефисы';
            }
            if (mb_strlen($slug) > 100) {
                $errors['slug'] = 'Slug не должен превышать 100 символов';
            }
        }

        return $errors;
    }

    /**
     * Оптимизировать SEO данные
     */
    public function optimizeSeoData(array $data): array
    {
        $optimized = $data;

        // Оптимизация SEO заголовка
        if (isset($optimized['seo_title'])) {
            $optimized['seo_title'] = $this->optimizeTitle($optimized['seo_title']);
        }

        // Оптимизация SEO описания
        if (isset($optimized['seo_description'])) {
            $optimized['seo_description'] = $this->optimizeDescription($optimized['seo_description']);
        }

        // Оптимизация H1 заголовка
        if (isset($optimized['seo_h1_title'])) {
            $optimized['seo_h1_title'] = $this->optimizeH1($optimized['seo_h1_title']);
        }

        return $optimized;
    }

    /**
     * Получить название из модели
     */
    private function getTitleFromModel(Model $model): string
    {
        // Пытаемся получить название из разных полей
        $titleFields = ['name', 'title', 'subject'];
        
        foreach ($titleFields as $field) {
            if (isset($model->$field) && !empty($model->$field)) {
                return $model->$field;
            }
        }

        // Если модель использует HasContent трейт
        if (method_exists($model, 'getContentField')) {
            try {
                return $model->getContentField('title', 'raw') ?: '';
            } catch (\Exception $e) {
                // Игнорируем ошибки
            }
        }

        return '';
    }

    /**
     * Получить описание из модели
     */
    private function getDescriptionFromModel(Model $model): string
    {
        // Пытаемся получить описание из разных полей
        $descriptionFields = ['description', 'summary', 'excerpt'];
        
        foreach ($descriptionFields as $field) {
            if (isset($model->$field) && !empty($model->$field)) {
                return $model->$field;
            }
        }

        // Если модель использует HasContent трейт
        if (method_exists($model, 'getContentField')) {
            try {
                $description = $model->getContentField('summary', 'raw');
                if (!empty($description)) {
                    return $description;
                }
            } catch (\Exception $e) {
                // Игнорируем ошибки
            }
        }

        // Пытаемся извлечь из основного контента
        $contentFields = ['content', 'body', 'text'];
        foreach ($contentFields as $field) {
            if (isset($model->$field) && !empty($model->$field)) {
                $content = $model->$field;
                $text = strip_tags($content);
                if (!empty($text)) {
                    return Str::limit($text, 160);
                }
            }
        }

        return '';
    }

    /**
     * Получить описание по умолчанию
     */
    private function getDefaultSeoDescription(): string
    {
        return 'Добро пожаловать на ' . config('app.name') . ' - ваш надежный партнер в мире качественных услуг и продуктов.';
    }

    /**
     * Проверить существование slug
     */
    private function slugExists(string $slug, ?int $excludeId, string $modelClass): bool
    {
        $query = $modelClass::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Получить класс модели по умолчанию
     */
    private function getDefaultModelClass(): string
    {
        // Можно настроить в конфиге
        return config('seo.default_model', \App\Models\Article::class);
    }

    /**
     * Оптимизировать заголовок
     */
    private function optimizeTitle(string $title): string
    {
        // Убираем лишние пробелы
        $title = trim($title);
        
        // Убираем HTML теги
        $title = strip_tags($title);
        
        // Первая буква заглавная
        $title = mb_ucfirst($title);
        
        return $title;
    }

    /**
     * Оптимизировать описание
     */
    private function optimizeDescription(string $description): string
    {
        // Убираем лишние пробелы
        $description = trim($description);
        
        // Убираем HTML теги
        $description = strip_tags($description);
        
        // Нормализуем пробелы
        $description = preg_replace('/\s+/', ' ', $description);
        
        return $description;
    }

    /**
     * Оптимизировать H1 заголовок
     */
    private function optimizeH1(string $h1): string
    {
        // Убираем лишние пробелы
        $h1 = trim($h1);
        
        // Убираем HTML теги
        $h1 = strip_tags($h1);
        
        // Первая буква заглавная
        $h1 = mb_ucfirst($h1);
        
        return $h1;
    }
}
