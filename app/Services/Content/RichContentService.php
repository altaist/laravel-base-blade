<?php

namespace App\Services\Content;

class RichContentService
{
    /**
     * Базовая структура RichContent
     */
    private array $baseStructure = [
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
     * Поддерживаемые типы блоков
     */
    private array $supportedBlockTypes = [
        'text', 'heading', 'image', 'video', 'quote', 'code', 'list', 'table'
    ];

    /**
     * Обработать RichContent
     */
    public function processRichContent(array $richContent): array
    {
        // Объединяем с базовой структурой
        $processed = array_merge($this->baseStructure, $richContent);
        
        // Валидируем структуру
        $processed = $this->validateStructure($processed);
        
        // Обрабатываем блоки
        $processed['blocks'] = is_array($processed['blocks']) 
            ? $this->processBlocks($processed['blocks']) 
            : [];
        
        // Обновляем метаданные
        $processed['metadata'] = $this->updateMetadata($processed);
        
        return $processed;
    }

    /**
     * Создать RichContent из обычного контента
     */
    public function createFromContent(string $content, string $type = 'html'): array
    {
        $richContent = $this->baseStructure;
        $richContent['type'] = $type;
        $richContent['data'] = $content;
        
        // Парсим контент на блоки
        $richContent['blocks'] = $this->parseContentToBlocks($content, $type);
        
        // Обновляем метаданные
        $richContent['metadata'] = $this->updateMetadata($richContent);
        
        return $richContent;
    }

    /**
     * Создать блок
     */
    public function createBlock(string $type, mixed $content, array $metadata = []): array
    {
        if (!in_array($type, $this->supportedBlockTypes)) {
            throw new \InvalidArgumentException("Unsupported block type: {$type}");
        }

        return [
            'id' => $this->generateBlockId(),
            'type' => $type,
            'content' => $content,
            'metadata' => array_merge($this->getDefaultBlockMetadata($type), $metadata),
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Экспортировать RichContent в HTML
     */
    public function exportToHtml(array $richContent): string
    {
        $html = '';
        
        // Добавляем основной контент
        if (!empty($richContent['data'])) {
            $html .= $this->processContentForHtml($richContent['data'], $richContent['type']);
        }
        
        // Добавляем блоки
        foreach ($richContent['blocks'] as $block) {
            $html .= $this->blockToHtml($block);
        }
        
        return $html;
    }

    /**
     * Экспортировать RichContent в Markdown
     */
    public function exportToMarkdown(array $richContent): string
    {
        $markdown = '';
        
        // Добавляем основной контент
        if (!empty($richContent['data'])) {
            $markdown .= $this->processContentForMarkdown($richContent['data'], $richContent['type']);
        }
        
        // Добавляем блоки
        foreach ($richContent['blocks'] as $block) {
            $markdown .= $this->blockToMarkdown($block);
        }
        
        return $markdown;
    }

    /**
     * Получить статистику RichContent
     */
    public function getStats(array $richContent): array
    {
        $stats = [
            'total_blocks' => count($richContent['blocks']),
            'block_types' => [],
            'word_count' => 0,
            'reading_time' => 0,
        ];
        
        // Подсчитываем блоки по типам
        foreach ($richContent['blocks'] as $block) {
            $type = $block['type'];
            $stats['block_types'][$type] = ($stats['block_types'][$type] ?? 0) + 1;
            
            // Подсчитываем слова в текстовых блоках
            if (in_array($type, ['text', 'heading', 'quote'])) {
                $text = is_string($block['content']) ? $block['content'] : '';
                $stats['word_count'] += str_word_count(strip_tags($text));
            }
        }
        
        // Время чтения (200 слов в минуту)
        $stats['reading_time'] = ceil($stats['word_count'] / 200);
        
        return $stats;
    }

    /**
     * Валидировать структуру RichContent
     */
    private function validateStructure(array $richContent): array
    {
        // Проверяем обязательные поля
        if (!isset($richContent['type'])) {
            $richContent['type'] = 'html';
        }
        
        if (!isset($richContent['data'])) {
            $richContent['data'] = '';
        }
        
        if (!isset($richContent['blocks'])) {
            $richContent['blocks'] = [];
        }
        
        if (!isset($richContent['metadata'])) {
            $richContent['metadata'] = $this->baseStructure['metadata'];
        }
        
        return $richContent;
    }

    /**
     * Обработать блоки
     */
    private function processBlocks(array $blocks): array
    {
        $processedBlocks = [];
        
        foreach ($blocks as $block) {
            if (is_array($block) && $this->isValidBlock($block)) {
                $processedBlocks[] = $this->processBlock($block);
            }
        }
        
        return $processedBlocks;
    }

    /**
     * Проверить валидность блока
     */
    private function isValidBlock(array $block): bool
    {
        return isset($block['type']) && 
               isset($block['content']) && 
               in_array($block['type'], $this->supportedBlockTypes);
    }

    /**
     * Обработать отдельный блок
     */
    private function processBlock(array $block): array
    {
        // Добавляем ID если его нет
        if (!isset($block['id'])) {
            $block['id'] = $this->generateBlockId();
        }
        
        // Добавляем метаданные по умолчанию
        if (!isset($block['metadata'])) {
            $block['metadata'] = $this->getDefaultBlockMetadata($block['type']);
        }
        
        // Добавляем время создания
        if (!isset($block['created_at'])) {
            $block['created_at'] = now()->toISOString();
        }
        
        return $block;
    }

    /**
     * Парсить контент на блоки
     */
    private function parseContentToBlocks(string $content, string $type): array
    {
        $blocks = [];
        
        if ($type === 'html') {
            $blocks = $this->parseHtmlToBlocks($content);
        } else {
            // Для других типов создаем один текстовый блок
            $blocks[] = $this->createBlock('text', $content);
        }
        
        return $blocks;
    }

    /**
     * Парсить HTML на блоки
     */
    private function parseHtmlToBlocks(string $html): array
    {
        $blocks = [];
        
        // Простой парсинг - можно расширить с помощью DOMDocument
        $patterns = [
            '/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i' => 'heading',
            '/<p[^>]*>(.*?)<\/p>/i' => 'text',
            '/<blockquote[^>]*>(.*?)<\/blockquote>/i' => 'quote',
            '/<img[^>]*>/i' => 'image',
        ];
        
        foreach ($patterns as $pattern => $blockType) {
            if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $content = $blockType === 'heading' ? $match[2] : $match[1];
                    $metadata = $blockType === 'heading' ? ['level' => (int)$match[1]] : [];
                    
                    $blocks[] = $this->createBlock($blockType, $content, $metadata);
                }
            }
        }
        
        return $blocks;
    }

    /**
     * Конвертировать блок в HTML
     */
    private function blockToHtml(array $block): string
    {
        $content = htmlspecialchars($block['content'], ENT_QUOTES, 'UTF-8');
        
        switch ($block['type']) {
            case 'heading':
                $level = $block['metadata']['level'] ?? 1;
                return "<h{$level}>{$content}</h{$level}>";
            case 'text':
                return "<p>{$content}</p>";
            case 'quote':
                return "<blockquote>{$content}</blockquote>";
            case 'code':
                return "<pre><code>{$content}</code></pre>";
            case 'image':
                $alt = $block['metadata']['alt'] ?? '';
                $src = $block['metadata']['src'] ?? $content;
                return "<img src=\"{$src}\" alt=\"{$alt}\">";
            default:
                return "<div>{$content}</div>";
        }
    }

    /**
     * Конвертировать блок в Markdown
     */
    private function blockToMarkdown(array $block): string
    {
        $content = $block['content'];
        
        switch ($block['type']) {
            case 'heading':
                $level = $block['metadata']['level'] ?? 1;
                $hashes = str_repeat('#', $level);
                return "{$hashes} {$content}\n\n";
            case 'text':
                return "{$content}\n\n";
            case 'quote':
                return "> {$content}\n\n";
            case 'code':
                return "```\n{$content}\n```\n\n";
            case 'image':
                $alt = $block['metadata']['alt'] ?? '';
                return "![{$alt}]({$content})\n\n";
            default:
                return "{$content}\n\n";
        }
    }

    /**
     * Обработать контент для HTML
     */
    private function processContentForHtml(string $content, string $type): string
    {
        if ($type === 'html') {
            return $content;
        }
        
        // Конвертируем в HTML
        return nl2br(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Обработать контент для Markdown
     */
    private function processContentForMarkdown(string $content, string $type): string
    {
        if ($type === 'markdown') {
            return $content;
        }
        
        // Простая конвертация
        return strip_tags($content);
    }

    /**
     * Обновить метаданные
     */
    private function updateMetadata(array $richContent): array
    {
        $metadata = $richContent['metadata'] ?? $this->baseStructure['metadata'];
        
        // Обновляем статистику
        $stats = $this->getStats($richContent);
        $metadata['word_count'] = $stats['word_count'];
        $metadata['reading_time'] = $stats['reading_time'];
        $metadata['last_processed'] = now()->toISOString();
        
        return $metadata;
    }

    /**
     * Получить метаданные по умолчанию для типа блока
     */
    private function getDefaultBlockMetadata(string $type): array
    {
        $defaults = [
            'text' => ['style' => 'paragraph'],
            'heading' => ['level' => 1],
            'image' => ['alt' => '', 'caption' => ''],
            'quote' => ['author' => '', 'source' => ''],
            'code' => ['language' => ''],
            'list' => ['ordered' => false],
            'table' => ['headers' => []],
        ];
        
        return $defaults[$type] ?? [];
    }

    /**
     * Сгенерировать уникальный ID для блока
     */
    private function generateBlockId(): string
    {
        return 'block_' . uniqid() . '_' . substr(md5(microtime()), 0, 8);
    }
}
