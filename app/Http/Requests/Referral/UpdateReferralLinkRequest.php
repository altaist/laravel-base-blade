<?php

namespace App\Http\Requests\Referral;

use App\Enums\Referral\ReferralLinkType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReferralLinkRequest extends FormRequest
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
            'type' => ['nullable', Rule::enum(ReferralLinkType::class)],
            'max_uses' => 'nullable|integer|min:1|max:10000',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Название ссылки не может быть длиннее 255 символов',
            'type.enum' => 'Некорректный тип ссылки',
            'max_uses.integer' => 'Максимальное количество использований должно быть числом',
            'max_uses.min' => 'Минимальное количество использований: 1',
            'max_uses.max' => 'Максимальное количество использований: 10000',
            'expires_at.date' => 'Некорректная дата истечения',
            'expires_at.after' => 'Дата истечения должна быть в будущем',
            'is_active.boolean' => 'Статус активности должен быть true или false',
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
