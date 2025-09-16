<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            return response()->json(['error' => 'Токен не предоставлен'], 400);
        }

        $userData = $this->authLinkService->getUserByAutoAuthToken($token);
        
        if ($userData) {
            return response()->json([
                'success' => true,
                'user' => $userData,
                'message' => 'Токен действителен'
            ]);
        }

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
            return response()->json(['error' => 'Токен не предоставлен'], 400);
        }

        $user = $this->authLinkService->validateAutoAuthToken($token);
        
        if (!$user) {
            return response()->json(['error' => 'Токен недействителен'], 400);
        }

        // Авторизуем пользователя
        Auth::login($user);
        
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