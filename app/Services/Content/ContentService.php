<?php

namespace App\Services\Content;

class ContentService
{
    /**
     * Обработать контент в зависимости от типа
     */
    public function processContent(mixed $content, string $format = 'html'): mixed
    {
        if (empty($content)) {
            return $content;
        }

        switch ($format) {
            case 'html':
                return $this->processHtmlContent($content);
            case 'text':
                return $this->processTextContent($content);
            case 'markdown':
                return $this->processMarkdownContent($content);
            case 'raw':
                return $content;
            default:
                return $this->processHtmlContent($content);
        }
    }

    /**
     * Обработать HTML контент
     */
    public function processHtmlContent(string $content): string
    {
        // Очистка HTML от потенциально опасных тегов
        $allowedTags = '<p><br><strong><b><em><i><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><blockquote><code><pre>';
        $content = strip_tags($content, $allowedTags);
        
        // Очистка атрибутов, оставляем только безопасные
        $content = $this->cleanHtmlAttributes($content);
        
        return trim($content);
    }

    /**
     * Обработать текстовый контент
     */
    public function processTextContent(string $content): string
    {
        // Удаляем HTML теги
        $content = strip_tags($content);
        
        // Декодируем HTML сущности
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        
        // Нормализуем пробелы
        $content = preg_replace('/\s+/', ' ', $content);
        
        return trim($content);
    }

    /**
     * Обработать Markdown контент
     */
    public function processMarkdownContent(string $content): string
    {
        // Базовая обработка Markdown (можно расширить с помощью библиотеки)
        $content = $this->processTextContent($content);
        
        // Простые замены Markdown
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
        $content = preg_replace('/`(.*?)`/', '<code>$1</code>', $content);
        
        return $content;
    }

    /**
     * Извлечь чистый текст из контента
     */
    public function extractTextFromContent(mixed $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Если это HTML, извлекаем текст
        if (is_string($content) && $this->containsHtml($content)) {
            $content = strip_tags($content);
        }

        // Декодируем HTML сущности
        $content = html_entity_decode($content, ENT_QUOTES, 'UTF-8');
        
        // Нормализуем пробелы
        $content = preg_replace('/\s+/', ' ', $content);
        
        return trim($content);
    }

    /**
     * Валидировать контент
     */
    public function validateContent(mixed $content, array $rules = []): array
    {
        $errors = [];

        if (empty($content)) {
            if (isset($rules['required']) && $rules['required']) {
                $errors[] = 'Контент обязателен для заполнения';
            }
            return $errors;
        }

        // Проверка длины
        if (isset($rules['min_length'])) {
            $textLength = mb_strlen($this->extractTextFromContent($content));
            if ($textLength < $rules['min_length']) {
                $errors[] = "Контент должен содержать минимум {$rules['min_length']} символов";
            }
        }

        if (isset($rules['max_length'])) {
            $textLength = mb_strlen($this->extractTextFromContent($content));
            if ($textLength > $rules['max_length']) {
                $errors[] = "Контент не должен превышать {$rules['max_length']} символов";
            }
        }

        // Проверка на запрещенные слова
        if (isset($rules['forbidden_words']) && is_array($rules['forbidden_words'])) {
            $text = strtolower($this->extractTextFromContent($content));
            foreach ($rules['forbidden_words'] as $word) {
                if (strpos($text, strtolower($word)) !== false) {
                    $errors[] = "Контент содержит запрещенное слово: {$word}";
                }
            }
        }

        return $errors;
    }

    /**
     * Получить статистику контента
     */
    public function getContentStats(mixed $content): array
    {
        $text = $this->extractTextFromContent($content);
        
        $wordCount = str_word_count($text);
        $charCount = mb_strlen($text);
        $charCountNoSpaces = mb_strlen(str_replace(' ', '', $text));
        
        // Примерное время чтения (200 слов в минуту)
        $readingTime = $wordCount > 0 ? ceil($wordCount / 200) : 0;
        
        return [
            'word_count' => $wordCount,
            'char_count' => $charCount,
            'char_count_no_spaces' => $charCountNoSpaces,
            'reading_time_minutes' => $readingTime,
            'paragraph_count' => $this->countParagraphs($content),
            'sentence_count' => $this->countSentences($text),
        ];
    }

    /**
     * Конвертировать контент в другой формат
     */
    public function convertContent(mixed $content, string $fromFormat, string $toFormat): string
    {
        // Сначала приводим к тексту
        $text = $this->extractTextFromContent($content);
        
        // Затем конвертируем в нужный формат
        switch ($toFormat) {
            case 'html':
                return $this->textToHtml($text);
            case 'markdown':
                return $this->textToMarkdown($text);
            case 'text':
                return $text;
            default:
                return $text;
        }
    }

    /**
     * Очистить HTML атрибуты, оставив только безопасные
     */
    private function cleanHtmlAttributes(string $content): string
    {
        $allowedAttributes = ['href', 'src', 'alt', 'title', 'class', 'id'];
        
        // Простая очистка - можно расширить с помощью DOMDocument
        foreach ($allowedAttributes as $attr) {
            $content = preg_replace_callback(
                "/<(\w+)([^>]*?)>/i",
                function ($matches) use ($attr) {
                    $tag = $matches[1];
                    $attributes = $matches[2];
                    
                    // Оставляем только разрешенные атрибуты
                    if (preg_match("/{$attr}=[\"']([^\"']*)[\"']/i", $attributes, $attrMatches)) {
                        return "<{$tag} {$attr}=\"{$attrMatches[1]}\">";
                    }
                    
                    return "<{$tag}>";
                },
                $content
            );
        }
        
        return $content;
    }

    /**
     * Проверить, содержит ли контент HTML
     */
    private function containsHtml(string $content): bool
    {
        return $content !== strip_tags($content);
    }

    /**
     * Подсчитать количество абзацев
     */
    private function countParagraphs(mixed $content): int
    {
        if (is_string($content) && $this->containsHtml($content)) {
            return substr_count($content, '<p>') + substr_count($content, '<div>');
        }
        
        return substr_count($content, "\n\n") + 1;
    }

    /**
     * Подсчитать количество предложений
     */
    private function countSentences(string $text): int
    {
        $sentences = preg_split('/[.!?]+/', $text);
        return count(array_filter($sentences, fn($s) => trim($s) !== ''));
    }

    /**
     * Конвертировать текст в HTML
     */
    private function textToHtml(string $text): string
    {
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $text = nl2br($text);
        return $text;
    }

    /**
     * Конвертировать текст в Markdown
     */
    private function textToMarkdown(string $text): string
    {
        // Простая конвертация - можно расширить
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        return $text;
    }
}
