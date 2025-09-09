<?php

namespace Tests\Unit\Services\Content;

use App\Services\Content\ContentService;
use Tests\TestCase;

class ContentServiceTest extends TestCase
{
    private ContentService $contentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contentService = new ContentService();
    }

    /** @test */
    public function it_processes_html_content_correctly()
    {
        $html = '<p>Hello <strong>world</strong>!</p><script>alert("xss")</script>';
        
        $result = $this->contentService->processHtmlContent($html);
        
        $this->assertStringContainsString('<p>Hello <strong>world</strong>!</p>', $result);
        $this->assertStringNotContainsString('<script>', $result);
    }

    /** @test */
    public function it_processes_text_content_correctly()
    {
        $html = '<p>Hello <strong>world</strong>!</p>';
        
        $result = $this->contentService->processTextContent($html);
        
        $this->assertEquals('Hello world!', $result);
    }

    /** @test */
    public function it_handles_empty_content()
    {
        $this->assertEquals('', $this->contentService->processHtmlContent(''));
        $this->assertEquals('', $this->contentService->processTextContent(''));
    }

    /** @test */
    public function it_processes_content_by_format()
    {
        $content = '<p>Hello <strong>world</strong>!</p>';
        
        $htmlResult = $this->contentService->processContent($content, 'html');
        $textResult = $this->contentService->processContent($content, 'text');
        $rawResult = $this->contentService->processContent($content, 'raw');
        
        $this->assertStringContainsString('<strong>', $htmlResult);
        $this->assertStringNotContainsString('<strong>', $textResult);
        $this->assertEquals($content, $rawResult);
    }

    /** @test */
    public function it_extracts_text_from_content()
    {
        $html = '<p>Hello <strong>world</strong>!</p><div>More text</div>';
        
        $result = $this->contentService->extractTextFromContent($html);
        
        $this->assertStringContainsString('Hello world!', $result);
        $this->assertStringContainsString('More text', $result);
    }

    /** @test */
    public function it_generates_content_stats()
    {
        $content = 'This is a test content with multiple words for testing purposes.';
        
        $stats = $this->contentService->getContentStats($content);
        
        $this->assertArrayHasKey('word_count', $stats);
        $this->assertArrayHasKey('reading_time_minutes', $stats);
        $this->assertArrayHasKey('char_count', $stats);
        $this->assertGreaterThan(0, $stats['word_count']);
    }

    /** @test */
    public function it_handles_exceptions_gracefully()
    {
        // Тестируем обработку исключений в processContent
        $result = $this->contentService->processContent('test', 'invalid_format');
        
        // Должен вернуть обработанный контент даже при неверном формате
        $this->assertIsString($result);
    }
}
