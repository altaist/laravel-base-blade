<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAuthLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expires_in_minutes' => 'nullable|integer|min:1|max:1440',
            'author_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'expires_in_minutes.integer' => 'Время жизни должно быть числом',
            'expires_in_minutes.min' => 'Минимальное время жизни: 1 минута',
            'expires_in_minutes.max' => 'Максимальное время жизни: 24 часа',
            'author_id.exists' => 'Указанный автор не найден',
        ];
    }
}
