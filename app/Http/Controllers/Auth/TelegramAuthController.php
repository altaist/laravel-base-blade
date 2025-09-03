<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TelegramAuthController extends Controller
{
    public function callback(Request $request)
    {
        if (!$this->checkTelegramAuthorization($request->all())) {
            return redirect()->route('login')->with('error', 'Неверная авторизация Telegram');
        }

        $user = User::where('telegram_id', $request->id)->first();

        if (!$user) {
            $user = User::create([
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'email' => $request->id . '@telegram.user',
                'password' => Hash::make(Str::random(32)),
                'telegram_id' => $request->id,
                'telegram_username' => $request->username,
                'role' => UserRole::USER,
            ]);
        } else {
            $user->update([
                'telegram_username' => $request->username,
            ]);
        }

        Auth::login($user);

        return redirect()->intended(route('profile'));
    }

    private function checkTelegramAuthorization(array $auth_data): bool
    {
        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);
        
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', config('telegram.bot.token'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        
        return $hash === $check_hash;
    }
}
