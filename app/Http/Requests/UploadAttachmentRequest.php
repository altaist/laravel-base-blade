<?php

namespace App\Http\Requests;

use App\Enums\AttachmentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UploadAttachmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file|max:10240', // 10MB
            'related_type' => 'required|string',
            'related_id' => 'required|integer|min:1',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => ['nullable', Rule::enum(AttachmentType::class)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Файл обязателен для загрузки',
            'file.file' => 'Загруженный файл недействителен',
            'file.max' => 'Размер файла не должен превышать 10MB',
            'related_type.required' => 'Тип связанной модели обязателен',
            'related_id.required' => 'ID связанной модели обязателен',
            'related_id.integer' => 'ID связанной модели должен быть числом',
            'related_id.min' => 'ID связанной модели должен быть больше 0',
            'name.max' => 'Название не должно превышать 255 символов',
            'description.max' => 'Описание не должно превышать 1000 символов',
        ];
    }
}
