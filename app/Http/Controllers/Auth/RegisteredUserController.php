<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Models\User;
use App\Services\Referral\ReferralService;
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
        private UserService $userService,
        private ReferralService $referralService
    ) {}

    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('pages.auth.register');
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

            // Обработка реферальной регистрации
            $referralProcessed = $this->referralService->processReferralRegistration($user);
            
            if ($referralProcessed) {
                Log::info('Пользователь зарегистрирован по реферальной ссылке', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
            }

            event(new Registered($user));

            Auth::login($user);

            $message = $referralProcessed 
                ? 'Регистрация успешна! Добро пожаловать! Вы зарегистрированы по реферальной ссылке.'
                : 'Регистрация успешна! Добро пожаловать!';

            return redirect()->route('dashboard')->with('success', $message);
            
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