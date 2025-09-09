<?php

namespace App\Traits;

use App\Services\Content\SeoService;

trait HasSeo
{
    /**
     * SEO поля для модели
     */
    protected $seoFields = [
        'seo_title',
        'seo_description', 
        'seo_h1_title',
        'slug'
    ];

    /**
     * Получить SEO заголовок
     */
    public function getSeoTitle(): string
    {
        if ($this->seo_title) {
            return $this->seo_title;
        }

        return app(SeoService::class)->generateSeoTitle($this);
    }

    /**
     * Получить SEO описание
     */
    public function getSeoDescription(): string
    {
        if ($this->seo_description) {
            return $this->seo_description;
        }

        return app(SeoService::class)->generateSeoDescription($this);
    }

    /**
     * Получить H1 заголовок
     */
    public function getSeoH1Title(): string
    {
        if ($this->seo_h1_title) {
            return $this->seo_h1_title;
        }

        return app(SeoService::class)->generateH1Title($this);
    }

    /**
     * Получить slug
     */
    public function getSlug(): string
    {
        if ($this->slug) {
            return $this->slug;
        }

        return app(SeoService::class)->generateSlug($this);
    }

    /**
     * Генерировать slug из названия
     */
    public function generateSlugFromName(?int $excludeId = null): string
    {
        $name = $this->getContentField('title', 'raw') ?? $this->name ?? '';
        return app(SeoService::class)->generateSlug($name, $excludeId);
    }

    /**
     * Автоматически генерировать все SEO поля из контента
     */
    public function generateSeoFromContent(): void
    {
        $seoData = app(SeoService::class)->generateSeoFromContent($this);
        
        $this->update([
            'seo_title' => $seoData['seo_title'],
            'seo_description' => $seoData['seo_description'],
            'seo_h1_title' => $seoData['seo_h1_title'],
        ]);
    }

    /**
     * Обновить SEO поля
     */
    public function updateSeoFields(array $seoData): void
    {
        $updateData = [];
        
        foreach ($this->seoFields as $field) {
            if (isset($seoData[$field])) {
                $updateData[$field] = $seoData[$field];
            }
        }
        
        if (!empty($updateData)) {
            $this->update($updateData);
        }
    }

    /**
     * Валидировать SEO данные
     */
    public function validateSeoData(array $data): array
    {
        return app(SeoService::class)->validateSeoData($data);
    }

    /**
     * Получить все SEO поля
     */
    public function getAllSeoFields(): array
    {
        $seo = [];
        
        foreach ($this->seoFields as $field) {
            $seo[$field] = $this->getAttribute($field);
        }
        
        return $seo;
    }

    /**
     * Проверить, заполнены ли SEO поля
     */
    public function hasSeoFields(): bool
    {
        foreach ($this->seoFields as $field) {
            if (!empty($this->getAttribute($field))) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Получить SEO данные для мета-тегов
     */
    public function getSeoMetaData(): array
    {
        return [
            'title' => $this->getSeoTitle(),
            'description' => $this->getSeoDescription(),
            'h1' => $this->getSeoH1Title(),
            'slug' => $this->getSlug(),
        ];
    }

    /**
     * Проверить уникальность slug
     */
    public function isSlugUnique(string $slug, ?int $excludeId = null): bool
    {
        return app(SeoService::class)->isSlugUnique($slug, $excludeId, static::class);
    }

    /**
     * Получить URL на основе slug
     */
    public function getSeoUrl(string $routeName = null): string
    {
        $slug = $this->getSlug();
        
        if ($routeName) {
            return route($routeName, $slug);
        }
        
        // Пытаемся определить маршрут автоматически
        $modelName = strtolower(class_basename(static::class));
        $routeName = $modelName . '.show';
        
        if (Route::has($routeName)) {
            return route($routeName, $slug);
        }
        
        return url($slug);
    }

    /**
     * Обновить slug если изменилось название
     */
    public function updateSlugIfNameChanged(): void
    {
        $currentName = $this->getContentField('title', 'raw') ?? $this->name ?? '';
        $originalName = $this->getOriginal('name') ?? '';
        
        if ($currentName !== $originalName && !empty($currentName)) {
            $newSlug = $this->generateSlugFromName($this->id);
            $this->update(['slug' => $newSlug]);
        }
    }
}
