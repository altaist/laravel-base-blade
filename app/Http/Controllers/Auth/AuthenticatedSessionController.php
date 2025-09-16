<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Services\AuthLinkService;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private AuthLinkService $authLinkService
    ) {}
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('pages.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

            // Генерируем токен автологина после успешной авторизации
            $this->generateAutoAuthToken($request);

            return redirect()->route('dashboard')
                ->with('success', 'Добро пожаловать, ' . Auth::user()->name . '!');
                
        } catch (ValidationException $e) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'email' => 'Неверный email или пароль.',
                ]);
        } catch (\Exception $e) {
            Log::error('Ошибка при входе: ' . $e->getMessage());
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'error' => 'Произошла техническая ошибка при входе. Пожалуйста, попробуйте позже или обратитесь в поддержку.'
                ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        try {
            // Очищаем токены автологина при ручном выходе
            $user = Auth::user();
            if ($user) {
                $this->authLinkService->deleteAutoAuthTokens($user);
                Log::info('Токены автологина очищены при ручном выходе', [
                    'user_id' => $user->id,
                    'user_email' => $user->email
                ]);
            }

            Auth::guard('web')->logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Вы успешно вышли из системы.');
            
        } catch (\Exception $e) {
            Log::error('Ошибка при выходе: ' . $e->getMessage());
            return back()->withErrors([
                'error' => 'Произошла ошибка при выходе из системы. Пожалуйста, попробуйте еще раз.'
            ]);
        }
    }

    /**
     * Генерировать токен автологина после успешной авторизации
     */
    private function generateAutoAuthToken(Request $request): void
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return;
            }

            $options = [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'author_id' => $user->id,
            ];

            $authLink = $this->authLinkService->generateAutoAuthToken($user, $options);
            
            // Устанавливаем куки на 30 дней через JavaScript
            $request->session()->put('auto_auth_token', $authLink->token);

            Log::info('Токен автологина создан после входа', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'ip' => $request->ip()
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка создания токена автологина: ' . $e->getMessage());
        }
    }
}