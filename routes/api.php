<?php

use App\Http\Controllers\Reactions\FavoriteController;
use App\Http\Controllers\Reactions\LikeController;
use App\Http\Controllers\Files\AttachmentController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\Content\ArticleController;
use App\Http\Controllers\Content\StatusController;
use App\Http\Controllers\AutoAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Auto Auth routes with rate limiting
Route::prefix('auto-auth')->middleware(['throttle:'.config('features.auto_auth.rate_limit', 10).',1'])->group(function () {
    Route::post('/check', [AutoAuthController::class, 'check'])->name('api.auto-auth.check');
    Route::post('/confirm', [AutoAuthController::class, 'confirm'])->name('api.auto-auth.confirm');
    Route::post('/reject', [AutoAuthController::class, 'reject'])->name('api.auto-auth.reject');
    Route::post('/generate', [AutoAuthController::class, 'generate'])
        ->middleware('auth:sanctum')
        ->name('api.auto-auth.generate');
});

Route::prefix('telegram')->group(function () {
    Route::post('{botId}/webhook', [TelegramWebhookController::class, 'handleWebhook'])
        ->where('botId', 'bot|admin_bot'); // Ограничиваем возможные значения botId
    Route::post('process-updates', [TelegramWebhookController::class, 'processUpdatesManually'])
        ->middleware('auth:sanctum'); // Защищаем ручной метод авторизацией
});

// Attachment routes
Route::middleware(['auth:sanctum'])->prefix('attachments')->group(function () {
    Route::post('upload', [AttachmentController::class, 'uploadAttachment']);
    Route::delete('{attachment}', [AttachmentController::class, 'deleteAttachment']);
    Route::get('{relatedType}/{relatedId}', [AttachmentController::class, 'getAttachments'])
        ->where('relatedType', '[a-zA-Z\\\\]+')
        ->where('relatedId', '[0-9]+');
});

// Like routes
Route::middleware(['auth:sanctum'])->prefix('likes')->group(function () {
    Route::post('/', [LikeController::class, 'store']);
    Route::post('toggle', [LikeController::class, 'toggle']);
    Route::delete('{likeableType}/{likeableId}', [LikeController::class, 'destroy'])
        ->where('likeableType', '[a-zA-Z\\\\]+')
        ->where('likeableId', '[0-9]+');
    Route::get('users/{user}', [LikeController::class, 'userLikes']);
});

// Favorite routes
Route::middleware(['auth:sanctum'])->prefix('favorites')->group(function () {
    Route::post('/', [FavoriteController::class, 'store']);
    Route::post('toggle', [FavoriteController::class, 'toggle']);
    Route::delete('{favoritableType}/{favoritableId}', [FavoriteController::class, 'destroy'])
        ->where('favoritableType', '[a-zA-Z\\\\]+')
        ->where('favoritableId', '[0-9]+');
    Route::get('users/{user}', [FavoriteController::class, 'userFavorites']);
});

// Article routes
Route::middleware(['auth:sanctum'])->prefix('articles')->group(function () {
    Route::get('/', [ArticleController::class, 'apiIndex']);
    Route::post('/', [ArticleController::class, 'apiStore']);
    Route::get('{article}', [ArticleController::class, 'apiShow']);
    Route::put('{article}', [ArticleController::class, 'apiUpdate']);
    Route::delete('{article}', [ArticleController::class, 'apiDestroy']);
});

// Status routes
Route::middleware(['auth:sanctum'])->prefix('status')->group(function () {
    Route::post('{article}/change', [StatusController::class, 'apiChangeStatus']);
    Route::post('{article}/publish', [StatusController::class, 'apiPublish']);
    Route::post('{article}/unpublish', [StatusController::class, 'apiUnpublish']);
    Route::post('{article}/ready', [StatusController::class, 'apiMarkAsReady']);
    Route::post('{article}/draft', [StatusController::class, 'apiMarkAsDraft']);
    Route::get('{article}/available', [StatusController::class, 'apiAvailableStatuses']);
});