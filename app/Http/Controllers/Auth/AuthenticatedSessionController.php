<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();

            $request->session()->regenerate();

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
}