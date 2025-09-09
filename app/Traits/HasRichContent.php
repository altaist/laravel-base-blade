<?php

namespace App\Traits;

use App\Services\Content\RichContentService;

trait HasRichContent
{
    /**
     * JSON структура для rich_content
     */
    protected $richContentStructure = [
        'type' => 'html',
        'data' => '',
        'blocks' => [],
        'metadata' => [
            'word_count' => 0,
            'reading_time' => 0,
            'last_processed' => null,
            'version' => '1.0'
        ]
    ];

    /**
     * Получить RichContent
     */
    public function getRichContent(): array
    {
        $richContent = $this->getAttribute('rich_content');
        
        if (empty($richContent)) {
            return $this->richContentStructure;
        }
        
        // Если это строка, пытаемся декодировать JSON
        if (is_string($richContent)) {
            $decoded = json_decode($richContent, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return array_merge($this->richContentStructure, $decoded);
            }
        }
        
        // Если это массив, объединяем с базовой структурой
        if (is_array($richContent)) {
            return array_merge($this->richContentStructure, $richContent);
        }
        
        return $this->richContentStructure;
    }

    /**
     * Установить RichContent
     */
    public function setRichContent(array $richContent): void
    {
        $processedContent = app(RichContentService::class)->processRichContent($richContent);
        $this->setAttribute('rich_content', $processedContent);
    }

    /**
     * Проверить, есть ли RichContent
     */
    public function hasRichContent(): bool
    {
        $richContent = $this->getRichContent();
        return !empty($richContent['data']) || !empty($richContent['blocks']);
    }

    /**
     * Получить блоки RichContent
     */
    public function getRichContentBlocks(): array
    {
        $richContent = $this->getRichContent();
        return $richContent['blocks'] ?? [];
    }

    /**
     * Добавить блок в RichContent
     */
    public function addRichContentBlock(string $type, mixed $content, array $metadata = []): void
    {
        $richContent = $this->getRichContent();
        $newBlock = app(RichContentService::class)->createBlock($type, $content, $metadata);
        
        $richContent['blocks'][] = $newBlock;
        $this->setRichContent($richContent);
    }

    /**
     * Удалить блок из RichContent
     */
    public function removeRichContentBlock(string $blockId): void
    {
        $richContent = $this->getRichContent();
        $richContent['blocks'] = array_filter(
            $richContent['blocks'],
            fn($block) => ($block['id'] ?? '') !== $blockId
        );
        
        $this->setRichContent($richContent);
    }

    /**
     * Переставить блоки в RichContent
     */
    public function reorderRichContentBlocks(array $newOrder): void
    {
        $richContent = $this->getRichContent();
        $blocks = $richContent['blocks'] ?? [];
        $reorderedBlocks = [];
        
        foreach ($newOrder as $blockId) {
            $block = collect($blocks)->firstWhere('id', $blockId);
            if ($block) {
                $reorderedBlocks[] = $block;
            }
        }
        
        $richContent['blocks'] = $reorderedBlocks;
        $this->setRichContent($richContent);
    }

    /**
     * Получить RichContent в HTML формате
     */
    public function getRichContentAsHtml(): string
    {
        return app(RichContentService::class)->exportToHtml($this->getRichContent());
    }

    /**
     * Получить RichContent в Markdown формате
     */
    public function getRichContentAsMarkdown(): string
    {
        return app(RichContentService::class)->exportToMarkdown($this->getRichContent());
    }

    /**
     * Получить метаданные RichContent
     */
    public function getRichContentMetadata(): array
    {
        $richContent = $this->getRichContent();
        return $richContent['metadata'] ?? [];
    }

    /**
     * Обновить метаданные RichContent
     */
    public function updateRichContentMetadata(array $metadata): void
    {
        $richContent = $this->getRichContent();
        $richContent['metadata'] = array_merge($richContent['metadata'] ?? [], $metadata);
        $this->setRichContent($richContent);
    }

    /**
     * Создать RichContent из обычного контента
     */
    public function createRichContentFromContent(string $content, string $type = 'html'): void
    {
        $richContent = app(RichContentService::class)->createFromContent($content, $type);
        $this->setRichContent($richContent);
    }

    /**
     * Синхронизировать RichContent с обычным контентом
     */
    public function syncRichContentWithContent(): void
    {
        if ($this->hasContentField('body')) {
            $content = $this->getContentField('body', 'raw');
            if ($content) {
                $this->createRichContentFromContent($content);
            }
        }
    }

    /**
     * Получить статистику RichContent
     */
    public function getRichContentStats(): array
    {
        $richContent = $this->getRichContent();
        return app(RichContentService::class)->getStats($richContent);
    }
}
