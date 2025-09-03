<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GenerateRegistrationLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'role' => 'nullable|string|in:admin,user',
            'telegram_id' => 'nullable|string|unique:users',
            'telegram_username' => 'nullable|string|max:255',
            'expires_in_minutes' => 'nullable|integer|min:1|max:1440',
            'author_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Имя обязательно для заполнения',
            'name.max' => 'Имя не может быть длиннее 255 символов',
            'email.email' => 'Введите корректный email адрес',
            'email.unique' => 'Пользователь с таким email уже существует',
            'role.in' => 'Некорректная роль',
            'telegram_id.unique' => 'Пользователь с таким Telegram ID уже существует',
            'expires_in_minutes.integer' => 'Время жизни должно быть числом',
            'expires_in_minutes.min' => 'Минимальное время жизни: 1 минута',
            'expires_in_minutes.max' => 'Максимальное время жизни: 24 часа',
            'author_id.exists' => 'Указанный автор не найден',
        ];
    }
}
