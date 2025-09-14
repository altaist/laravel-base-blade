<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Enums\ArticleStatus;
use App\Services\Content\ArticleService;
use App\Services\Content\ContentService;
use App\Services\Content\RichContentService;
use App\Services\Content\SeoService;
use App\Services\Content\MediaService;
use App\Traits\ValidatesContent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    use ValidatesContent;

    public function __construct(
        private ArticleService $articleService,
        private SeoService $seoService,
        private ContentService $contentService,
        private RichContentService $richContentService,
        private MediaService $mediaService
    ) {}

    /**
     * Показать список статей
     */
    public function index(Request $request): View
    {
        $status = $request->get('status');
        $user = $request->get('user_id');

        $query = Article::query();

        if ($status) {
            $query->where('status', $status);
        } else {
            $query->where('status', ArticleStatus::PUBLISHED);
        }

        if ($user) {
            $query->where('user_id', $user);
        }

        $articles = $query->with([
            'likes' => function($q) { $q->select('id', 'likeable_id', 'likeable_type'); },
            'favorites' => function($q) { $q->select('id', 'favoritable_id', 'favoritable_type'); }
        ])->orderBy('created_at', 'desc')->paginate(10);

        $popularArticles = Article::where('status', ArticleStatus::PUBLISHED)
            ->with([
                'likes' => function($q) { $q->select('id', 'likeable_id', 'likeable_type'); },
                'favorites' => function($q) { $q->select('id', 'favoritable_id', 'favoritable_type'); }
            ])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get();

        return view('pages.public.articles.index', compact('articles', 'popularArticles'));
    }

    /**
     * Показать форму создания статьи
     */
    public function create(): View
    {
        return view('pages.admin.articles.create');
    }

    /**
     * Сохранить новую статью
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->getArticleValidationRules());

        $validated['user_id'] = Auth::id();
        $validated['slug'] = $this->seoService->generateSlug($validated['name'], null, Article::class);

        $article = $this->articleService->create($validated);

        return redirect()->route('articles.show', $article)
            ->with('success', 'Статья успешно создана');
    }

    /**
     * Показать статью
     */
    public function show(Article $article): View
    {
        return view('pages.public.articles.show', compact('article'));
    }

    /**
     * Показать форму редактирования статьи
     */
    public function edit(Article $article): View
    {
        return view('pages.admin.articles.edit', compact('article'));
    }

    /**
     * Обновить статью
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate($this->getArticleUpdateValidationRules());

        // Если название изменилось, генерируем новый slug
        if ($validated['name'] !== $article->name) {
            $validated['slug'] = $this->seoService->generateSlug($validated['name'], $article->id, Article::class);
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

        return view('pages.public.articles.show', compact('article'));
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
        $validated = $request->validate($this->getArticleValidationRules());

        $validated['user_id'] = Auth::id();
        $validated['slug'] = $this->seoService->generateSlug($validated['name'], null, Article::class);

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
        $validated = $request->validate($this->getArticleUpdateValidationRules());

        // Если название изменилось, генерируем новый slug
        if ($validated['name'] !== $article->name) {
            $validated['slug'] = $this->seoService->generateSlug($validated['name'], $article->id, Article::class);
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

    /**
     * API: Получить RichContent статьи
     */
    public function apiGetRichContent(Article $article): JsonResponse
    {
        return response()->json([
            'rich_content' => $article->getRichContent(),
            'has_rich_content' => $article->hasRichContent(),
        ]);
    }

    /**
     * API: Обновить RichContent статьи
     */
    public function apiUpdateRichContent(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'rich_content' => 'required|array',
        ]);

        $article->setRichContent($validated['rich_content']);

        return response()->json([
            'rich_content' => $article->getRichContent(),
            'message' => 'RichContent успешно обновлен',
        ]);
    }

    /**
     * API: Добавить блок в RichContent
     */
    public function apiAddRichContentBlock(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:text,heading,image,video,quote,code,list,table',
            'content' => 'required',
            'metadata' => 'nullable|array',
        ]);

        $article->addRichContentBlock(
            $validated['type'],
            $validated['content'],
            $validated['metadata'] ?? []
        );

        return response()->json([
            'rich_content' => $article->getRichContent(),
            'message' => 'Блок успешно добавлен',
        ]);
    }

    /**
     * API: Удалить блок из RichContent
     */
    public function apiRemoveRichContentBlock(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'block_id' => 'required|string',
        ]);

        $article->removeRichContentBlock($validated['block_id']);

        return response()->json([
            'rich_content' => $article->getRichContent(),
            'message' => 'Блок успешно удален',
        ]);
    }

    /**
     * API: Получить контент в HTML формате
     */
    public function apiGetContentAsHtml(Article $article): JsonResponse
    {
        $html = '';
        
        // Добавляем обычный контент
        if ($article->hasContentField('body')) {
            $html .= $article->getContentField('body', 'html');
        }
        
        // Добавляем RichContent если есть
        if ($article->hasRichContent()) {
            $html .= $article->getRichContentAsHtml();
        }

        return response()->json([
            'html' => $html,
        ]);
    }

    /**
     * API: Получить контент в Markdown формате
     */
    public function apiGetContentAsMarkdown(Article $article): JsonResponse
    {
        $markdown = '';
        
        // Добавляем обычный контент
        if ($article->hasContentField('body')) {
            $markdown .= $article->getContentField('body', 'markdown');
        }
        
        // Добавляем RichContent если есть
        if ($article->hasRichContent()) {
            $markdown .= $article->getRichContentAsMarkdown();
        }

        return response()->json([
            'markdown' => $markdown,
        ]);
    }

    /**
     * API: Получить статистику контента
     */
    public function apiGetContentStats(Article $article): JsonResponse
    {
        return response()->json([
            'content_stats' => $article->getContentStats(),
            'rich_content_stats' => $article->hasRichContent() ? $article->getRichContentStats() : null,
        ]);
    }

    /**
     * API: Синхронизировать RichContent с обычным контентом
     */
    public function apiSyncRichContent(Article $article): JsonResponse
    {
        $article->syncRichContentWithContent();

        return response()->json([
            'rich_content' => $article->getRichContent(),
            'message' => 'RichContent синхронизирован с обычным контентом',
        ]);
    }
}
