<?php

namespace App\Services\Content;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleSeoService
{
    /**
     * Генерирует SEO заголовок на основе названия статьи
     */
    public function generateSeoTitle(Article $article): string
    {
        if ($article->seo_title) {
            return $article->seo_title;
        }

        return $article->name . ' | ' . config('app.name');
    }

    /**
     * Генерирует SEO описание на основе контента статьи
     */
    public function generateSeoDescription(Article $article): string
    {
        if ($article->seo_description) {
            return $article->seo_description;
        }

        if ($article->description) {
            return Str::limit($article->description, 160);
        }

        // Извлекаем текст из HTML контента
        $text = strip_tags($article->content);
        return Str::limit($text, 160);
    }

    /**
     * Генерирует H1 заголовок для статьи
     */
    public function generateH1Title(Article $article): string
    {
        if ($article->seo_h1_title) {
            return $article->seo_h1_title;
        }

        return $article->name;
    }

    /**
     * Генерирует slug для статьи
     */
    public function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Проверяет существование slug
     */
    private function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $query = Article::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Обновляет SEO поля статьи
     */
    public function updateSeoFields(Article $article, array $seoData): Article
    {
        $updateData = [];

        if (isset($seoData['seo_title'])) {
            $updateData['seo_title'] = $seoData['seo_title'];
        }

        if (isset($seoData['seo_description'])) {
            $updateData['seo_description'] = $seoData['seo_description'];
        }

        if (isset($seoData['seo_h1_title'])) {
            $updateData['seo_h1_title'] = $seoData['seo_h1_title'];
        }

        if (!empty($updateData)) {
            $article->update($updateData);
        }

        return $article;
    }

    /**
     * Автоматически генерирует все SEO поля для статьи
     */
    public function autoGenerateSeo(Article $article): Article
    {
        $seoData = [
            'seo_title' => $this->generateSeoTitle($article),
            'seo_description' => $this->generateSeoDescription($article),
            'seo_h1_title' => $this->generateH1Title($article),
        ];

        return $this->updateSeoFields($article, $seoData);
    }

    /**
     * Валидирует SEO данные
     */
    public function validateSeoData(array $data): array
    {
        $errors = [];

        if (isset($data['seo_title']) && strlen($data['seo_title']) > 60) {
            $errors['seo_title'] = 'SEO заголовок не должен превышать 60 символов';
        }

        if (isset($data['seo_description']) && strlen($data['seo_description']) > 160) {
            $errors['seo_description'] = 'SEO описание не должно превышать 160 символов';
        }

        if (isset($data['seo_h1_title']) && strlen($data['seo_h1_title']) > 100) {
            $errors['seo_h1_title'] = 'H1 заголовок не должен превышать 100 символов';
        }

        return $errors;
    }
}
