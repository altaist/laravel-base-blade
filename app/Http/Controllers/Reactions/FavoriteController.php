<?php

namespace App\Http\Controllers\Reactions;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Reactions\FavoriteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteService $favoriteService
    ) {}

    /**
     * Добавить в избранное
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'favoritable_type' => 'required|string',
            'favoritable_id' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $favoritableType = $request->favoritable_type;
        $favoritableId = $request->favoritable_id;

        // Получаем класс из морфинг маппинга
        $favoritableClass = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($favoritableType);
        if (!$favoritableClass) {
            return response()->json(['error' => 'Invalid favoritable type'], 400);
        }

        // Получаем сущность
        $favoritable = $favoritableClass::find($favoritableId);
        if (!$favoritable) {
            return response()->json(['error' => 'Favoritable entity not found'], 404);
        }

        // Добавляем в избранное
        $favorite = $this->favoriteService->addToFavorites($user, $favoritable);

        return response()->json([
            'message' => 'Added to favorites successfully',
            'favorite' => $favorite,
            'is_favorited' => true,
            'favorites_count' => $this->favoriteService->getFavoritesCount($favoritable),
        ], 201);
    }

    /**
     * Убрать из избранного
     */
    public function destroy(string $favoritableType, int $favoritableId): JsonResponse
    {
        $user = Auth::user();

        // Получаем класс из морфинг маппинга
        $favoritableClass = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($favoritableType);
        if (!$favoritableClass) {
            return response()->json(['error' => 'Invalid favoritable type'], 400);
        }

        // Получаем сущность
        $favoritable = $favoritableClass::find($favoritableId);
        if (!$favoritable) {
            return response()->json(['error' => 'Favoritable entity not found'], 404);
        }

        // Убираем из избранного
        $removed = $this->favoriteService->removeFromFavorites($user, $favoritable);

        if (!$removed) {
            return response()->json(['error' => 'Favorite not found'], 404);
        }

        return response()->json([
            'message' => 'Removed from favorites successfully',
            'is_favorited' => false,
            'favorites_count' => $this->favoriteService->getFavoritesCount($favoritable),
        ]);
    }

    /**
     * Переключить избранное
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'favoritable_type' => 'required|string',
            'favoritable_id' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $favoritableType = $request->favoritable_type;
        $favoritableId = $request->favoritable_id;

        // Получаем класс из морфинг маппинга
        $favoritableClass = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($favoritableType);
        if (!$favoritableClass) {
            return response()->json(['error' => 'Invalid favoritable type'], 400);
        }

        // Получаем сущность
        $favoritable = $favoritableClass::find($favoritableId);
        if (!$favoritable) {
            return response()->json(['error' => 'Favoritable entity not found'], 404);
        }

        // Переключаем избранное
        $isFavorited = $this->favoriteService->toggleFavorite($user, $favoritable);

        // Обновляем модель после изменения
        $favoritable->refresh();

        return response()->json([
            'message' => $isFavorited ? 'Added to favorites successfully' : 'Removed from favorites successfully',
            'is_favorited' => $isFavorited,
            'favorites_count' => $this->favoriteService->getFavoritesCount($favoritable),
        ]);
    }

    /**
     * Получить избранное пользователя
     */
    public function userFavorites(User $user, Request $request): JsonResponse
    {
        $type = $request->get('type');
        $favorites = $this->favoriteService->getUserFavorites($user, $type);

        return response()->json([
            'favorites' => $favorites,
            'count' => $favorites->count(),
        ]);
    }
}
