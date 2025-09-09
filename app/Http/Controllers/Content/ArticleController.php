<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\Content\ArticleService;
use App\Services\Content\ArticleSeoService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function __construct(
        private ArticleService $articleService,
        private ArticleSeoService $seoService
    ) {}

    /**
     * Показать список статей
     */
    public function index(Request $request): View
    {
        $status = $request->get('status');
        $user = $request->get('user_id');

        if ($status) {
            $articles = $this->articleService->getByStatus($status);
        } elseif ($user) {
            $articles = $this->articleService->getByUser($user);
        } else {
            $articles = $this->articleService->getPublished();
        }

        return view('articles.index', compact('articles'));
    }

    /**
     * Показать форму создания статьи
     */
    public function create(): View
    {
        return view('articles.create');
    }

    /**
     * Сохранить новую статью
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_h1_title' => 'nullable|string|max:100',
            'img_file_id' => 'nullable|exists:files,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = $this->seoService->generateSlug($validated['name']);

        $article = $this->articleService->create($validated);

        return redirect()->route('articles.show', $article)
            ->with('success', 'Статья успешно создана');
    }

    /**
     * Показать статью
     */
    public function show(Article $article): View
    {
        return view('articles.show', compact('article'));
    }

    /**
     * Показать форму редактирования статьи
     */
    public function edit(Article $article): View
    {
        return view('articles.edit', compact('article'));
    }

    /**
     * Обновить статью
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_h1_title' => 'nullable|string|max:100',
            'img_file_id' => 'nullable|exists:files,id',
        ]);

        // Если название изменилось, генерируем новый slug
        if ($validated['name'] !== $article->name) {
            $validated['slug'] = $this->seoService->generateSlug($validated['name'], $article->id);
        }

        $this->articleService->update($article, $validated);

        return redirect()->route('articles.show', $article)
            ->with('success', 'Статья успешно обновлена');
    }

    /**
     * Удалить статью
     */
    public function destroy(Article $article): RedirectResponse
    {
        $this->articleService->delete($article);

        return redirect()->route('articles.index')
            ->with('success', 'Статья успешно удалена');
    }

    /**
     * Показать статью по slug
     */
    public function showBySlug(string $slug): View
    {
        $article = $this->articleService->findBySlug($slug);

        if (!$article) {
            abort(404);
        }

        return view('articles.show', compact('article'));
    }

    /**
     * API: Получить список статей
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $status = $request->get('status');
        $user = $request->get('user_id');

        if ($status) {
            $articles = $this->articleService->getByStatus($status);
        } elseif ($user) {
            $articles = $this->articleService->getByUser($user);
        } else {
            $articles = $this->articleService->getPublished();
        }

        return response()->json([
            'articles' => $articles,
            'count' => $articles->count(),
        ]);
    }

    /**
     * API: Получить статью
     */
    public function apiShow(Article $article): JsonResponse
    {
        return response()->json([
            'article' => $article->load(['user', 'imgFile']),
        ]);
    }

    /**
     * API: Создать статью
     */
    public function apiStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_h1_title' => 'nullable|string|max:100',
            'img_file_id' => 'nullable|exists:files,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = $this->seoService->generateSlug($validated['name']);

        $article = $this->articleService->create($validated);

        return response()->json([
            'article' => $article,
            'message' => 'Статья успешно создана',
        ], 201);
    }

    /**
     * API: Обновить статью
     */
    public function apiUpdate(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_h1_title' => 'nullable|string|max:100',
            'img_file_id' => 'nullable|exists:files,id',
        ]);

        // Если название изменилось, генерируем новый slug
        if ($validated['name'] !== $article->name) {
            $validated['slug'] = $this->seoService->generateSlug($validated['name'], $article->id);
        }

        $this->articleService->update($article, $validated);

        return response()->json([
            'article' => $article->fresh(),
            'message' => 'Статья успешно обновлена',
        ]);
    }

    /**
     * API: Удалить статью
     */
    public function apiDestroy(Article $article): JsonResponse
    {
        $this->articleService->delete($article);

        return response()->json([
            'message' => 'Статья успешно удалена',
        ]);
    }
}
