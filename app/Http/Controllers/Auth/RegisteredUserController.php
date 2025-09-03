<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterUserRequest $request)
    {
        try {
            $user = $this->userService->createUserWithPerson([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('profile')->with('success', 'Регистрация успешна! Добро пожаловать!');
            
        } catch (\Exception $e) {
            Log::error('Ошибка при регистрации: ' . $e->getMessage());
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'error' => 'Произошла техническая ошибка при регистрации. Пожалуйста, попробуйте позже или обратитесь в поддержку.'
                ]);
        }
    }
}