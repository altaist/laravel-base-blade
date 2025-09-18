<?php

namespace App\Http\Controllers;

use App\Services\Telegram\TelegramService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Контроллер для обработки вебхуков от Telegram
 * 
 * Для установки вебхука используйте:
 * https://api.telegram.org/bot{token}/setWebhook?url=https://your-domain.com/api/telegram/webhook
 * 
 * Для удаления вебхука:
 * https://api.telegram.org/bot{token}/deleteWebhook
 * 
 * Для проверки статуса вебхука:
 * https://api.telegram.org/bot{token}/getWebhookInfo
 */
class TelegramWebhookController extends Controller
{
    public function __construct(
        private readonly TelegramService $telegramService
    ) {}

    /**
     * Обработка входящих сообщений от Telegram через вебхук
     */
    public function handleWebhook(Request $request, string $botId): JsonResponse
    {
        try {
            // Валидация botId
            if (!config("telegram.bots.{$botId}.token")) {
                Log::channel('telegram')->error('Unknown bot ID', ['bot_id' => $botId]);
                return response()->json(['status' => 'error', 'message' => 'Unknown bot'], 400);
            }

            $update = $request->all();
            
            if (empty($update)) {
                Log::channel('telegram')->warning('Empty Telegram webhook request received', ['bot_id' => $botId]);
                return response()->json(['status' => 'error', 'message' => 'Empty request']);
            }

            $this->telegramService->handleIncomingMessage($update, $botId);
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('Failed to handle Telegram webhook', [
                'bot_id' => $botId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Internal error'], 500);
        }
    }

    /**
     * Ручная обработка обновлений от Telegram
     * Полезно, когда вебхук не настроен или для тестирования
     */
    public function processUpdatesManually(string $botId = 'main'): JsonResponse
    {
        try {
            $token = config("telegram.bots.{$botId}.token");
            
            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Bot '{$botId}' not configured"
                ], 400);
            }

            $apiUrl = config('telegram.api_url', 'https://api.telegram.org');
            $response = \Illuminate\Support\Facades\Http::get(
                "{$apiUrl}/bot{$token}/getUpdates"
            );

            if (!$response->successful()) {
                throw new \Exception('Failed to get updates from Telegram: ' . $response->body());
            }

            $updates = $response->json('result', []);
            $processed = 0;

            foreach ($updates as $update) {
                $this->telegramService->handleIncomingMessage($update, $botId);
                $processed++;
            }

            return response()->json([
                'status' => 'ok',
                'processed' => $processed,
                'message' => "Successfully processed {$processed} updates"
            ]);
        } catch (\Exception $e) {
            Log::channel('telegram')->error('Failed to process Telegram updates manually', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process updates: ' . $e->getMessage()
            ], 500);
        }
    }
}
