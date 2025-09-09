<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserEditRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Авторизация проверяется в middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;
        
        return [
            // Основная информация пользователя
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'nullable', 
                'email', 
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'role' => ['required', 'in:admin,manager,user'],
            
            // Личная информация
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female'],
            
            // Адрес
            'address.street' => ['nullable', 'string', 'max:255'],
            'address.house' => ['nullable', 'string', 'max:20'],
            'address.apartment' => ['nullable', 'string', 'max:20'],
            'address.city' => ['nullable', 'string', 'max:255'],
            'address.postal_code' => ['nullable', 'string', 'regex:/^[0-9]{6}$/'],
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
            'name.required' => 'Имя пользователя обязательно для заполнения',
            'name.string' => 'Имя пользователя должно быть строкой',
            'name.max' => 'Имя пользователя не должно превышать 255 символов',
            'email.email' => 'Email должен быть корректным адресом электронной почты',
            'email.unique' => 'Пользователь с таким email уже существует',
            'role.required' => 'Роль обязательна для выбора',
            'role.in' => 'Выбранная роль недопустима',
            'birth_date.date' => 'Дата рождения должна быть корректной датой',
            'birth_date.before' => 'Дата рождения не может быть в будущем',
            'gender.in' => 'Выбранный пол недопустим',
            'address.postal_code.regex' => 'Почтовый индекс должен содержать 6 цифр',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Имя пользователя',
            'email' => 'Email',
            'role' => 'Роль',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'middle_name' => 'Отчество',
            'phone' => 'Телефон',
            'birth_date' => 'Дата рождения',
            'gender' => 'Пол',
            'address.street' => 'Улица',
            'address.house' => 'Дом',
            'address.apartment' => 'Квартира',
            'address.city' => 'Город',
            'address.postal_code' => 'Почтовый индекс',
        ];
    }
}
