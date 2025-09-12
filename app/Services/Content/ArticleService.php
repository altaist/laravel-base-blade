<?php

namespace App\Services\Content;

use App\Models\Article;
use App\Models\User;
use App\Enums\ArticleStatus;
use Illuminate\Support\Collection;

class ArticleService
{
    /**
     * Создать новую статью
     */
    public function create(array $data): Article
    {
        return Article::create($data);
    }
    
    /**
     * Обновить статью
     */
    public function update(Article $article, array $data): Article
    {
        $article->update($data);
        return $article->fresh();
    }
    
    /**
     * Удалить статью
     */
    public function delete(Article $article): bool
    {
        return $article->delete();
    }
    
    /**
     * Получить опубликованные статьи
     */
    public function getPublished(): Collection
    {
        return Article::where('status', ArticleStatus::PUBLISHED)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Получить статьи по статусу
     */
    public function getByStatus(string $status): Collection
    {
        return Article::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Получить статьи пользователя
     */
    public function getByUser(User $user): Collection
    {
        return Article::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    /**
     * Найти статью по slug
     */
    public function findBySlug(string $slug): ?Article
    {
        return Article::where('slug', $slug)
            ->where('status', ArticleStatus::PUBLISHED)
            ->first();
    }
    
    /**
     * Получить количество статей по статусам
     */
    public function getCountByStatus(): array
    {
        return Article::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
    
    /**
     * Получить популярные статьи
     */
    public function getPopularArticles(int $limit = 10): Collection
    {
        return Article::where('status', 'published')
            ->withCount('likes')
            ->orderBy('likes_count', 'desc')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Получить последние статьи
     */
    public function getLatestArticles(int $limit = 10): Collection
    {
        return Article::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
