<?php

namespace App\Traits;

use App\Services\Content\ContentService;

trait HasContent
{
    /**
     * Конфигурация полей контента для каждой модели
     * Переопределяется в конкретных моделях
     */
    protected $contentFields = [
        'title' => ['field' => 'name', 'type' => 'text', 'required' => true],
        'summary' => ['field' => 'description', 'type' => 'text', 'required' => false],
        'body' => ['field' => 'content', 'type' => 'html', 'required' => true],
        'rich' => ['field' => 'rich_content', 'type' => 'json', 'required' => false]
    ];

    /**
     * Получить контент по типу
     */
    public function getContentField(string $type, string $format = 'html'): mixed
    {
        if (!isset($this->contentFields[$type])) {
            throw new \InvalidArgumentException("Content field type '{$type}' not configured");
        }

        $fieldConfig = $this->contentFields[$type];
        $fieldName = $fieldConfig['field'];
        
        $value = $this->getAttribute($fieldName);
        
        if ($value && $format !== 'raw') {
            return app(ContentService::class)->processContent($value, $format);
        }
        
        return $value;
    }

    /**
     * Установить контент по типу
     */
    public function setContentField(string $type, mixed $value, string $contentType = 'html'): void
    {
        if (!isset($this->contentFields[$type])) {
            throw new \InvalidArgumentException("Content field type '{$type}' not configured");
        }

        $fieldConfig = $this->contentFields[$type];
        $fieldName = $fieldConfig['field'];
        
        $processedValue = app(ContentService::class)->processContent($value, $contentType);
        $this->setAttribute($fieldName, $processedValue);
    }

    /**
     * Получить контент для отображения в списках (name + description)
     */
    public function getListContent(): array
    {
        return [
            'title' => $this->getContentField('title'),
            'summary' => $this->getContentField('summary'),
        ];
    }

    /**
     * Получить все поля контента
     */
    public function getAllContentFields(): array
    {
        $content = [];
        
        foreach ($this->contentFields as $type => $config) {
            $content[$type] = $this->getContentField($type);
        }
        
        return $content;
    }

    /**
     * Установить все поля контента
     */
    public function setAllContentFields(array $contentData): void
    {
        foreach ($contentData as $type => $value) {
            if (isset($this->contentFields[$type])) {
                $this->setContentField($type, $value);
            }
        }
    }

    /**
     * Проверить, есть ли контент определенного типа
     */
    public function hasContentField(string $type): bool
    {
        if (!isset($this->contentFields[$type])) {
            return false;
        }

        $fieldConfig = $this->contentFields[$type];
        $fieldName = $fieldConfig['field'];
        
        $value = $this->getAttribute($fieldName);
        return !empty($value);
    }

    /**
     * Получить конфигурацию полей контента
     */
    public function getContentFieldsConfig(): array
    {
        return $this->contentFields;
    }

    /**
     * Получить статистику контента
     */
    public function getContentStats(): array
    {
        $stats = [];
        
        foreach ($this->contentFields as $type => $config) {
            $content = $this->getContentField($type, 'raw');
            if ($content) {
                $stats[$type] = app(ContentService::class)->getContentStats($content);
            }
        }
        
        return $stats;
    }
}
