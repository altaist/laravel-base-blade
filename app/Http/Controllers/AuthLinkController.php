<?php

namespace App\Http\Controllers;

use App\Services\AuthLinkService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'expires_in_minutes' => 'nullable|integer|min:1|max:1440',
            ]);

            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Необходима авторизация'], 401);
            }

            $options = [];
            if ($request->has('expires_in_minutes')) {
                $options['expires_in_minutes'] = $request->expires_in_minutes;
            }

            $authLink = $this->authLinkService->generateAuthLink($user, $options);

            $loginUrl = route('auth-link.authenticate', $authLink->token);

            return response()->json([
                'success' => true,
                'message' => 'Ссылка авторизации создана',
                'data' => [
                    'token' => $authLink->token,
                    'expires_at' => $authLink->expires_at,
                    'login_url' => $loginUrl,
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Ошибка валидации',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Ошибка при генерации ссылки авторизации: ' . $e->getMessage());
            return response()->json([
                'error' => 'Произошла техническая ошибка'
            ], 500);
        }
    }

    /**
     * Генерировать ссылку для автоматической регистрации
     */
    public function generateRegistrationLink(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|string|email|max:255|unique:users',
                'role' => 'nullable|string|in:admin,user',
                'telegram_id' => 'nullable|string|unique:users',
                'telegram_username' => 'nullable|string|max:255',
                'expires_in_minutes' => 'nullable|integer|min:1|max:1440',
            ]);

            $userData = $request->only(['name', 'email', 'role', 'telegram_id', 'telegram_username']);
            
            $options = [];
            if ($request->has('expires_in_minutes')) {
                $options['expires_in_minutes'] = $request->expires_in_minutes;
            }

            $authLink = $this->authLinkService->generateRegistrationLink($userData, $options);

            $registrationUrl = route('auth-link.authenticate', $authLink->token);

            return response()->json([
                'success' => true,
                'message' => 'Ссылка для регистрации создана',
                'data' => [
                    'token' => $authLink->token,
                    'expires_at' => $authLink->expires_at,
                    'registration_url' => $registrationUrl,
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Ошибка валидации',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Ошибка при генерации ссылки регистрации: ' . $e->getMessage());
            return response()->json([
                'error' => 'Произошла техническая ошибка'
            ], 500);
        }
    }

    /**
     * Авторизация по ссылке (универсальная - авторизация + регистрация)
     */
    public function authenticate(string $token)
    {
        try {
            $authLink = $this->authLinkService->validateAuthLink($token);

            if (!$authLink) {
                return redirect()->route('login')->with('error', 'Ссылка авторизации недействительна или истекла');
            }

            $user = null;

            if ($authLink->isForRegistration()) {
                // Автоматическая регистрация
                $user = $this->createUserFromAuthLink($authLink);
            } else {
                // Обычная авторизация
                $user = $authLink->user;
            }

            if (!$user) {
                return redirect()->route('login')->with('error', 'Ошибка при создании пользователя');
            }

            // Авторизуем пользователя
            Auth::login($user);

            // Удаляем ссылку
            $this->authLinkService->deleteAfterUse($token);

            $message = $authLink->isForRegistration() 
                ? 'Регистрация и вход выполнены успешно! Добро пожаловать!' 
                : 'Добро пожаловать!';

            return redirect()->route('profile')->with('success', $message);

        } catch (\Exception $e) {
            Log::error('Ошибка при авторизации по ссылке: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Произошла ошибка при авторизации');
        }
    }

    /**
     * Удалить все активные ссылки пользователя
     */
    public function revoke(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Необходима авторизация'], 401);
            }

            $count = $this->authLinkService->deleteActiveLinks($user);

            return response()->json([
                'success' => true,
                'message' => "Удалено {$count} активных ссылок",
                'data' => [
                    'deleted_count' => $count
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при удалении ссылок авторизации: ' . $e->getMessage());
            return response()->json([
                'error' => 'Произошла техническая ошибка'
            ], 500);
        }
    }

    /**
     * Получить статистику по ссылкам пользователя
     */
    public function stats(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json(['error' => 'Необходима авторизация'], 401);
            }

            $stats = $this->authLinkService->getLinksStats($user);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Ошибка при получении статистики ссылок: ' . $e->getMessage());
            return response()->json([
                'error' => 'Произошла техническая ошибка'
            ], 500);
        }
    }

    /**
     * Создать пользователя из данных ссылки регистрации
     */
    private function createUserFromAuthLink(AuthLink $authLink): ?User
    {
        try {
            $userData = [
                'name' => $authLink->name,
                'email' => $authLink->email,
                'role' => $authLink->role,
                'telegram_id' => $authLink->telegram_id,
                'telegram_username' => $authLink->telegram_username,
            ];

            // Убираем пустые значения
            $userData = array_filter($userData, function($value) {
                return $value !== null && $value !== '';
            });

            // Используем UserService для создания пользователя и персоны
            $user = $this->userService->createUserWithPerson($userData);

            return $user;

        } catch (\Exception $e) {
            Log::error('Ошибка при создании пользователя из ссылки: ' . $e->getMessage());
            return null;
        }
    }
}
