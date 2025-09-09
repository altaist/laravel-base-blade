<?php

namespace App\Traits;

trait ValidatesContent
{
    /**
     * Получить правила валидации для статьи
     */
    public function getArticleValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'rich_content' => 'nullable|array',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_h1_title' => 'nullable|string|max:100',
            'img_file_id' => 'nullable|exists:files,id',
        ];
    }

    /**
     * Получить правила валидации для обновления статьи
     */
    public function getArticleUpdateValidationRules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'sometimes|required|string',
            'rich_content' => 'nullable|array',
            'seo_title' => 'nullable|string|max:60',
            'seo_description' => 'nullable|string|max:160',
            'seo_h1_title' => 'nullable|string|max:100',
            'img_file_id' => 'nullable|exists:files,id',
        ];
    }
}
