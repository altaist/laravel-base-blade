<?php

namespace App\Http\Requests\Referral;

use App\Enums\Referral\ReferralLinkType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateReferralLinkRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'type' => ['required', Rule::enum(ReferralLinkType::class)],
            'max_uses' => 'nullable|integer|min:1|max:10000',
            'expires_at' => 'nullable|date|after:now',
            'redirect_url' => 'nullable|url|max:2048',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Название ссылки не может быть длиннее 255 символов',
            'type.required' => 'Тип ссылки обязателен для заполнения',
            'type.enum' => 'Некорректный тип ссылки',
            'max_uses.integer' => 'Максимальное количество использований должно быть числом',
            'max_uses.min' => 'Минимальное количество использований: 1',
            'max_uses.max' => 'Максимальное количество использований: 10000',
            'expires_at.date' => 'Некорректная дата истечения',
            'expires_at.after' => 'Дата истечения должна быть в будущем',
            'redirect_url.url' => 'Некорректный URL для перенаправления',
            'redirect_url.max' => 'URL для перенаправления не может быть длиннее 2048 символов',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('type') && is_string($this->type)) {
            $this->merge([
                'type' => ReferralLinkType::from($this->type),
            ]);
        }
    }
}
