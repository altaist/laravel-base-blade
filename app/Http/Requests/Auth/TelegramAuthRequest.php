<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TelegramAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'required|string',
            'first_name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255',
            'hash' => 'required|string',
            'auth_date' => 'required|string',
            'photo_url' => 'nullable|string|url',
            'last_name' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'ID пользователя Telegram обязателен',
            'first_name.required' => 'Имя пользователя Telegram обязательно',
            'first_name.max' => 'Имя не может быть длиннее 255 символов',
            'username.max' => 'Username не может быть длиннее 255 символов',
            'hash.required' => 'Хеш авторизации обязателен',
            'auth_date.required' => 'Дата авторизации обязательна',
            'photo_url.url' => 'URL фотографии должен быть корректным',
            'last_name.max' => 'Фамилия не может быть длиннее 255 символов',
        ];
    }
}
