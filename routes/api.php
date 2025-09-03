<?php

use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('telegram')->group(function () {
    Route::post('{botId}/webhook', [TelegramWebhookController::class, 'handleWebhook'])
        ->where('botId', 'bot|admin_bot'); // Ограничиваем возможные значения botId
    Route::post('process-updates', [TelegramWebhookController::class, 'processUpdatesManually'])
        ->middleware('auth:sanctum'); // Защищаем ручной метод авторизацией
});