<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonEditRequest extends FormRequest
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
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'region' => 'nullable|string|max:255',
            'gender' => 'nullable|string|in:male,female',
            'birth_date' => 'nullable|date|before:today',
            'age' => 'nullable|integer|min:0|max:150',
            
            // Адрес
            'address' => 'nullable|array',
            'address.street' => 'nullable|string|max:255',
            'address.house' => 'nullable|string|max:50',
            'address.apartment' => 'nullable|string|max:50',
            'address.city' => 'nullable|string|max:255',
            'address.postal_code' => 'nullable|string|max:20',
            
            // Дополнительная информация
            'additional_info' => 'nullable',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.max' => 'Имя не должно превышать 255 символов.',
            'last_name.max' => 'Фамилия не должна превышать 255 символов.',
            'middle_name.max' => 'Отчество не должно превышать 255 символов.',
            'email.email' => 'Введите корректный email адрес.',
            'email.max' => 'Email не должен превышать 255 символов.',
            'phone.max' => 'Телефон не должен превышать 20 символов.',
            'region.max' => 'Регион не должен превышать 255 символов.',
            'gender.in' => 'Пол должен быть мужской или женский.',
            'birth_date.date' => 'Введите корректную дату рождения.',
            'birth_date.before' => 'Дата рождения должна быть в прошлом.',
            'age.integer' => 'Возраст должен быть числом.',
            'age.min' => 'Возраст не может быть отрицательным.',
            'age.max' => 'Возраст не может превышать 150 лет.',
            'address.array' => 'Адрес должен быть массивом данных.',
            'address.street.max' => 'Название улицы не должно превышать 255 символов.',
            'address.house.max' => 'Номер дома не должен превышать 50 символов.',
            'address.apartment.max' => 'Номер квартиры не должен превышать 50 символов.',
            'address.city.max' => 'Название города не должно превышать 255 символов.',
            'address.postal_code.max' => 'Почтовый индекс не должен превышать 20 символов.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Очищаем пустые значения в массивах
        if ($this->has('address')) {
            $address = $this->input('address', []);
            // Проверяем, что это массив
            if (is_array($address)) {
                $address = array_filter($address, function ($value) {
                    return $value !== null && $value !== '';
                });
                $this->merge(['address' => $address]);
            }
        }

        if ($this->has('additional_info')) {
            $additionalInfo = $this->input('additional_info', []);
            // Проверяем, что это массив
            if (is_array($additionalInfo)) {
                $additionalInfo = array_filter($additionalInfo, function ($value) {
                    return $value !== null && $value !== '';
                });
                $this->merge(['additional_info' => $additionalInfo]);
            }
        }
    }
}
