<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\StatusService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StatusController extends Controller
{
    /**
     * Изменить статус статьи
     */
    public function changeStatus(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:draft,ready_to_publish,published',
        ]);

        StatusService::changeStatusFor($article, $validated['status']);

        return redirect()->back()
            ->with('success', 'Статус статьи изменен');
    }

    /**
     * Опубликовать статью
     */
    public function publish(Article $article): RedirectResponse
    {
        StatusService::publishFor($article);

        return redirect()->back()
            ->with('success', 'Статья опубликована');
    }

    /**
     * Снять с публикации
     */
    public function unpublish(Article $article): RedirectResponse
    {
        StatusService::unpublishFor($article);

        return redirect()->back()
            ->with('success', 'Статья снята с публикации');
    }

    /**
     * Отметить как готовую к публикации
     */
    public function markAsReady(Article $article): RedirectResponse
    {
        StatusService::markAsReadyFor($article);

        return redirect()->back()
            ->with('success', 'Статья отмечена как готовая к публикации');
    }

    /**
     * Вернуть в черновики
     */
    public function markAsDraft(Article $article): RedirectResponse
    {
        StatusService::markAsDraftFor($article);

        return redirect()->back()
            ->with('success', 'Статья возвращена в черновики');
    }

    /**
     * API: Изменить статус статьи
     */
    public function apiChangeStatus(Request $request, Article $article): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:draft,ready_to_publish,published',
        ]);

        StatusService::changeStatusFor($article, $validated['status']);

        return response()->json([
            'article' => $article->fresh(),
            'message' => 'Статус статьи изменен',
        ]);
    }

    /**
     * API: Опубликовать статью
     */
    public function apiPublish(Article $article): JsonResponse
    {
        StatusService::publishFor($article);

        return response()->json([
            'article' => $article->fresh(),
            'message' => 'Статья опубликована',
        ]);
    }

    /**
     * API: Снять с публикации
     */
    public function apiUnpublish(Article $article): JsonResponse
    {
        StatusService::unpublishFor($article);

        return response()->json([
            'article' => $article->fresh(),
            'message' => 'Статья снята с публикации',
        ]);
    }

    /**
     * API: Отметить как готовую к публикации
     */
    public function apiMarkAsReady(Article $article): JsonResponse
    {
        StatusService::markAsReadyFor($article);

        return response()->json([
            'article' => $article->fresh(),
            'message' => 'Статья отмечена как готовая к публикации',
        ]);
    }

    /**
     * API: Вернуть в черновики
     */
    public function apiMarkAsDraft(Article $article): JsonResponse
    {
        StatusService::markAsDraftFor($article);

        return response()->json([
            'article' => $article->fresh(),
            'message' => 'Статья возвращена в черновики',
        ]);
    }

    /**
     * API: Получить доступные статусы для статьи
     */
    public function apiAvailableStatuses(Article $article): JsonResponse
    {
        $availableStatuses = StatusService::getAvailableStatusesFor($article);

        return response()->json([
            'current_status' => $article->status->value,
            'available_statuses' => $availableStatuses,
        ]);
    }
}
