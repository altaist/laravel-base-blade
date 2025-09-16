<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AuthLinkService;

class AutoAuthController extends Controller
{
    public function __construct(
        private AuthLinkService $authLinkService
    ) {}

    /**
     * Проверить токен автологина
     * POST /api/auto-auth/check
     */
    public function check(Request $request)
    {
        $token = $request->input('token');
        
        if (!$token) {
            Log::channel('security')->warning('Попытка проверки автологина без токена', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return response()->json(['error' => 'Токен не предоставлен'], 400);
        }

        // Валидация формата токена
        if (!preg_match('/^auto_[a-zA-Z0-9]{20}$/', $token)) {
            Log::channel('security')->warning('Неверный формат токена автологина', [
                'ip' => $request->ip(),
                'token_preview' => substr($token, 0, 8) . '...'
            ]);
            return response()->json(['error' => 'Неверный формат токена'], 400);
        }

        Log::channel('security')->info('Попытка проверки токена автологина', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'token_preview' => substr($token, 0, 8) . '...'
        ]);

        $userData = $this->authLinkService->getUserByAutoAuthToken($token);
        
        if ($userData) {
            Log::channel('security')->info('Токен автологина валиден', [
                'ip' => $request->ip(),
                'user_id' => $userData['id'],
                'user_email' => $userData['email']
            ]);
            return response()->json([
                'success' => true,
                'user' => $userData,
                'message' => 'Токен действителен'
            ]);
        }

        Log::channel('security')->warning('Недействительный токен автологина', [
            'ip' => $request->ip(),
            'token_preview' => substr($token, 0, 8) . '...'
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Токен недействителен или истек'
        ], 400);
    }

    /**
     * Подтвердить автологин
     * POST /api/auto-auth/confirm
     */
    public function confirm(Request $request)
    {
        $token = $request->input('token');
        
        if (!$token) {
            Log::channel('security')->warning('Попытка подтверждения автологина без токена', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return response()->json(['error' => 'Токен не предоставлен'], 400);
        }

        $user = $this->authLinkService->validateAutoAuthToken($token);
        
        if (!$user) {
            Log::channel('security')->warning('Попытка подтверждения с недействительным токеном', [
                'ip' => $request->ip(),
                'token_preview' => substr($token, 0, 8) . '...'
            ]);
            return response()->json(['error' => 'Токен недействителен'], 400);
        }

        // Авторизуем пользователя
        Auth::login($user);
        
        Log::channel('security')->info('Успешный автологин', [
            'ip' => $request->ip(),
            'user_id' => $user->id,
            'user_email' => $user->email,
            'token_preview' => substr($token, 0, 8) . '...'
        ]);
        
        // Удаляем использованный токен
        $this->authLinkService->deleteAfterUse($token);

        return response()->json([
            'success' => true,
            'message' => 'Авторизация успешна',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * Отклонить автологин
     * POST /api/auto-auth/reject
     */
    public function reject(Request $request)
    {
        $token = $request->input('token');
        
        Log::channel('security')->info('Автологин отклонен пользователем', [
            'ip' => $request->ip(),
            'token_preview' => $token ? substr($token, 0, 8) . '...' : 'none'
        ]);
        
        if ($token) {
            $this->authLinkService->deleteAfterUse($token);
        }

        return response()->json([
            'success' => true,
            'message' => 'Автологин отклонен'
        ]);
    }

    /**
     * Генерировать токен автологина для текущего пользователя
     * POST /api/auto-auth/generate
     */
    public function generate(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Необходима авторизация'], 401);
        }

        $options = [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'author_id' => $user->id,
        ];

        $authLink = $this->authLinkService->generateAutoAuthToken($user, $options);
        
        return response()->json([
            'success' => true,
            'token' => $authLink->token,
            'expires_at' => $authLink->expires_at,
            'message' => 'Токен автологина создан'
        ]);
    }
}