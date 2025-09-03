<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\GenerateAuthLinkRequest;
use App\Http\Requests\Auth\GenerateRegistrationLinkRequest;
use App\Services\AuthLinkService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AuthLink;
use App\Models\User;

class AuthLinkController extends Controller
{
    public function __construct(
        private AuthLinkService $authLinkService,
        private UserService $userService
    ) {}

    /**
     * Генерировать ссылку авторизации
     */
    public function generate(GenerateAuthLinkRequest $request)
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $options = $this->prepareOptions($request, ['expires_in_minutes', 'author_id']);
        $options['ip_address'] = $request->ip();
        $options['user_agent'] = $request->userAgent();
        $options['author_id'] = $options['author_id'] ?? $user->id;

        $authLink = $this->authLinkService->generateAuthLink($user, $options);

        return $this->successResponse([
            'token' => $authLink->token,
            'expires_at' => $authLink->expires_at,
            'login_url' => route('auth-link.authenticate', $authLink->token),
            'author_id' => $authLink->author_id,
        ], 'Ссылка авторизации создана');
    }

    /**
     * Генерировать ссылку для автоматической регистрации
     */
    public function generateRegistrationLink(GenerateRegistrationLinkRequest $request)
    {
        $userData = $request->only(['name', 'email', 'role', 'telegram_id', 'telegram_username']);
        $options = $this->prepareOptions($request, ['expires_in_minutes', 'author_id']);
        $options['ip_address'] = $request->ip();
        $options['user_agent'] = $request->userAgent();
        $options['author_id'] = $options['author_id'] ?? Auth::id() ?? null;
        
        $authLink = $this->authLinkService->generateRegistrationLink($userData, $options);

        return $this->successResponse([
            'token' => $authLink->token,
            'expires_at' => $authLink->expires_at,
            'registration_url' => route('auth-link.authenticate', $authLink->token),
            'author_id' => $authLink->author_id,
        ], 'Ссылка для регистрации создана');
    }

    /**
     * Авторизация по ссылке (универсальная - авторизация + регистрация)
     * Доступно без авторизации
     */
    public function authenticate(string $token)
    {
        Log::info('Попытка авторизации по ссылке', [
            'token' => $token,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);

        try {
            $authLink = $this->authLinkService->validateAuthLink($token);

            if (!$authLink) {
                Log::warning('Недействительная ссылка авторизации', [
                    'token' => $token,
                    'ip' => request()->ip()
                ]);
                return redirect()->route('login')->with('error', 'Ссылка авторизации недействительна или истекла');
            }

            $user = $this->getOrCreateUser($authLink);
            if (!$user) {
                Log::error('Ошибка при создании пользователя из ссылки', [
                    'token' => $token,
                    'auth_link_id' => $authLink->id,
                    'ip' => request()->ip()
                ]);
                return redirect()->route('login')->with('error', 'Ошибка при создании пользователя');
            }

            Auth::login($user);
            $this->authLinkService->deleteAfterUse($token);

            $message = $authLink->isForRegistration() 
                ? 'Регистрация и вход выполнены успешно! Добро пожаловать!' 
                : 'Добро пожаловать!';

            Log::info('Успешная авторизация по ссылке', [
                'token' => $token,
                'user_id' => $user->id,
                'user_email' => $user->email,
                'is_registration' => $authLink->isForRegistration(),
                'ip' => request()->ip()
            ]);

            return redirect()->route('dashboard')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Критическая ошибка при авторизации по ссылке', [
                'token' => $token,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ip' => request()->ip(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('login')->with('error', 'Произошла ошибка при авторизации');
        }
    }

    /**
     * Удалить все активные ссылки пользователя
     */
    public function revoke(Request $request)
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $count = $this->authLinkService->deleteActiveLinks($user);

        return $this->successResponse([
            'deleted_count' => $count
        ], "Удалено {$count} активных ссылок");
    }

    /**
     * Получить статистику по ссылкам пользователя
     */
    public function stats(Request $request)
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            return $this->unauthorizedResponse();
        }

        $stats = $this->authLinkService->getLinksStats($user);
        return $this->successResponse($stats);
    }

    // ===== HELPER МЕТОДЫ =====

    /**
     * Получить авторизованного пользователя
     */
    private function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Подготовить опции для сервиса
     */
    private function prepareOptions(Request $request, array $allowedFields): array
    {
        $options = [];
        foreach ($allowedFields as $field) {
            if ($request->has($field)) {
                $options[$field] = $request->input($field);
            }
        }
        return $options;
    }

    /**
     * Получить или создать пользователя
     */
    private function getOrCreateUser(AuthLink $authLink): ?User
    {
        if ($authLink->isForRegistration()) {
            return $this->createUserFromAuthLink($authLink);
        }
        return $authLink->user;
    }

    /**
     * Создать пользователя из данных ссылки регистрации
     */
    private function createUserFromAuthLink(AuthLink $authLink): ?User
    {
        try {
            // Собираем только непустые данные
            $userData = [];
            
            if (!empty($authLink->telegram_id)) {
                $userData['telegram_id'] = $authLink->telegram_id;
            }
            
            if (!empty($authLink->telegram_username)) {
                $userData['telegram_username'] = $authLink->telegram_username;
            }
            
            // НЕ передаем name, email, role - они будут автогенерированы
            // name = "Пользователь", email = "случайный@user.generated", role = "user"

            return $this->userService->createUserWithAutoGeneratedData($userData);

        } catch (\Exception $e) {
            Log::error('Ошибка при создании пользователя из ссылки: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Успешный ответ
     */
    private function successResponse($data, string $message = 'Успешно'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Ответ об ошибке авторизации
     */
    private function unauthorizedResponse(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['error' => 'Необходима авторизация'], 401);
    }
}
