<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AdminUserCreateRequest extends FormRequest
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
        return [
            // Основная информация пользователя
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => ['required'],
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
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Email должен быть корректным адресом электронной почты',
            'email.unique' => 'Пользователь с таким email уже существует',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.confirmed' => 'Пароли не совпадают',
            'password.min' => 'Пароль должен содержать минимум 8 символов',
            'password_confirmation.required' => 'Подтверждение пароля обязательно',
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
            'email' => 'Email',
            'password' => 'Пароль',
            'password_confirmation' => 'Подтверждение пароля',
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
