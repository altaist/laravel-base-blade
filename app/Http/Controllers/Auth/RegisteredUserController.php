<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
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
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect()->route('profile')->with('success', 'Регистрация успешна! Добро пожаловать!');
            
        } catch (ValidationException $e) {
            return back()->withErrors([
                'name' => $e->errors()['name'] ?? [],
                'email' => $e->errors()['email'] ?? [],
                'password' => $e->errors()['password'] ?? [],
            ])->withInput($request->except('password'));
        } catch (\Exception $e) {
            \Log::error('Ошибка при регистрации: ' . $e->getMessage());
            return back()
                ->withInput($request->except('password'))
                ->withErrors([
                    'error' => 'Произошла техническая ошибка при регистрации. Пожалуйста, попробуйте позже или обратитесь в поддержку.'
                ]);
        }
    }
}