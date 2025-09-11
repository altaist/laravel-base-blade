<?php

namespace App\Http\Controllers\Reactions;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Reactions\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function __construct(
        private LikeService $likeService
    ) {}

    /**
     * Добавить лайк
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $likeableType = $request->likeable_type;
        $likeableId = $request->likeable_id;

        // Получаем класс из морфинг маппинга
        $likeableClass = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($likeableType);
        if (!$likeableClass) {
            return response()->json(['error' => 'Invalid likeable type'], 400);
        }

        // Получаем сущность
        $likeable = $likeableClass::find($likeableId);
        if (!$likeable) {
            return response()->json(['error' => 'Likeable entity not found'], 404);
        }

        // Добавляем лайк
        $like = $this->likeService->like($user, $likeable);

        return response()->json([
            'message' => 'Liked successfully',
            'like' => $like,
            'is_liked' => true,
            'likes_count' => $this->likeService->getLikesCount($likeable),
        ], 201);
    }

    /**
     * Убрать лайк
     */
    public function destroy(string $likeableType, int $likeableId): JsonResponse
    {
        $user = Auth::user();

        // Получаем класс из морфинг маппинга
        $likeableClass = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($likeableType);
        if (!$likeableClass) {
            return response()->json(['error' => 'Invalid likeable type'], 400);
        }

        // Получаем сущность
        $likeable = $likeableClass::find($likeableId);
        if (!$likeable) {
            return response()->json(['error' => 'Likeable entity not found'], 404);
        }

        // Убираем лайк
        $removed = $this->likeService->unlike($user, $likeable);

        if (!$removed) {
            return response()->json(['error' => 'Like not found'], 404);
        }

        return response()->json([
            'message' => 'Like removed successfully',
            'is_liked' => false,
            'likes_count' => $this->likeService->getLikesCount($likeable),
        ]);
    }

    /**
     * Переключить лайк
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'likeable_type' => 'required|string',
            'likeable_id' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $likeableType = $request->likeable_type;
        $likeableId = $request->likeable_id;

        // Получаем класс из морфинг маппинга
        $likeableClass = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($likeableType);
        if (!$likeableClass) {
            return response()->json(['error' => 'Invalid likeable type'], 400);
        }

        // Получаем сущность
        $likeable = $likeableClass::find($likeableId);
        if (!$likeable) {
            return response()->json(['error' => 'Likeable entity not found'], 404);
        }

        // Переключаем лайк
        $isLiked = $this->likeService->toggleLike($user, $likeable);

        // Обновляем модель после изменения
        $likeable->refresh();
        
        return response()->json([
            'message' => $isLiked ? 'Liked successfully' : 'Like removed successfully',
            'is_liked' => $isLiked,
            'likes_count' => $this->likeService->getLikesCount($likeable),
        ]);
    }

    /**
     * Получить лайки пользователя
     */
    public function userLikes(User $user, Request $request): JsonResponse
    {
        $type = $request->get('type');
        $likes = $this->likeService->getUserLikes($user, $type);

        return response()->json([
            'likes' => $likes,
            'count' => $likes->count(),
        ]);
    }
}
