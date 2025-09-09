<?php

namespace App\Http\Controllers\Admin\Articles;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Enums\ArticleStatus;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /**
     * Список статей
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        
        $query = Article::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }
        
        $articles = $query->orderBy('created_at', 'desc')->paginate(15);
        
        $articleStats = $this->getArticleStats();
        
        return view('admin.articles.index', compact('articles', 'search', 'articleStats'));
    }

    /**
     * Просмотр статьи
     */
    public function show(Article $article): View
    {
        return view('admin.articles.show', compact('article'));
    }

    /**
     * Форма создания статьи
     */
    public function create(): View
    {
        return view('admin.articles.create');
    }

    /**
     * Сохранение новой статьи
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug',
            'content' => 'required|string',
            'rich_content' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_column(ArticleStatus::cases(), 'value')),
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
        ]);

        try {
            $validated['user_id'] = Auth::id();
            $article = Article::create($validated);
            
            return redirect()
                ->route('admin.articles.show', $article)
                ->with('success', 'Статья успешно создана');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ошибка при создании статьи: ' . $e->getMessage()]);
        }
    }

    /**
     * Форма редактирования статьи
     */
    public function edit(Article $article): View
    {
        return view('admin.articles.edit', compact('article'));
    }

    /**
     * Обновление статьи
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articles,slug,' . $article->id,
            'content' => 'required|string',
            'rich_content' => 'nullable|string',
            'status' => 'required|in:' . implode(',', array_column(ArticleStatus::cases(), 'value')),
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
        ]);

        try {
            $article->update($validated);
            
            return redirect()
                ->route('admin.articles.show', $article)
                ->with('success', 'Статья успешно обновлена');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Ошибка при обновлении статьи: ' . $e->getMessage()]);
        }
    }

    /**
     * Удаление статьи
     */
    public function destroy(Article $article): RedirectResponse
    {
        try {
            $article->delete();
            
            return redirect()
                ->route('admin.articles.index')
                ->with('success', 'Статья успешно удалена');
                
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Получение статистики по статьям
     */
    private function getArticleStats(): array
    {
        $total = Article::count();
        $recent = Article::where('created_at', '>=', now()->subWeek())->count();
        $published = Article::where('status', ArticleStatus::PUBLISHED)->count();
        $draft = Article::where('status', ArticleStatus::DRAFT)->count();
        $ready = Article::where('status', ArticleStatus::READY_TO_PUBLISH)->count();

        return [
            'total' => $total,
            'recent' => $recent,
            'published' => $published,
            'draft' => $draft,
            'ready' => $ready,
        ];
    }
}
