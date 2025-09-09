<?php

namespace Tests\Unit\Services\Content;

use App\Services\Content\RichContentService;
use Tests\TestCase;

class RichContentServiceTest extends TestCase
{
    private RichContentService $richContentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->richContentService = new RichContentService();
    }

    /** @test */
    public function it_creates_rich_content_structure()
    {
        $result = $this->richContentService->createFromContent('Initial content', 'html');
        
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('blocks', $result);
        $this->assertArrayHasKey('metadata', $result);
        $this->assertEquals('html', $result['type']);
        $this->assertEquals('Initial content', $result['data']);
    }

    /** @test */
    public function it_processes_rich_content()
    {
        $richContent = [
            'type' => 'html',
            'data' => 'Test content',
            'blocks' => [
                [
                    'id' => 'block1',
                    'type' => 'text',
                    'content' => 'Hello world',
                    'metadata' => []
                ]
            ]
        ];
        
        $result = $this->richContentService->processRichContent($richContent);
        
        $this->assertArrayHasKey('metadata', $result);
        $this->assertArrayHasKey('word_count', $result['metadata']);
        $this->assertArrayHasKey('reading_time', $result['metadata']);
    }

    /** @test */
    public function it_adds_blocks_to_rich_content()
    {
        $richContent = $this->richContentService->createFromContent('');
        
        $newBlock = $this->richContentService->createBlock('text', 'New block content');
        $richContent['blocks'][] = $newBlock;
        $result = $this->richContentService->processRichContent($richContent);
        
        $this->assertCount(1, $result['blocks']);
        $this->assertEquals('text', $result['blocks'][0]['type']);
        $this->assertEquals('New block content', $result['blocks'][0]['content']);
    }

    /** @test */
    public function it_removes_blocks_from_rich_content()
    {
        $richContent = $this->richContentService->createFromContent('');
        $newBlock = $this->richContentService->createBlock('text', 'Block to remove');
        $richContent['blocks'][] = $newBlock;
        $blockId = $richContent['blocks'][0]['id'];
        
        // Удаляем блок из массива
        $richContent['blocks'] = array_filter($richContent['blocks'], fn($block) => $block['id'] !== $blockId);
        $result = $this->richContentService->processRichContent($richContent);
        
        $this->assertCount(0, $result['blocks']);
    }

    /** @test */
    public function it_exports_to_html()
    {
        $richContent = $this->richContentService->createFromContent('');
        $textBlock = $this->richContentService->createBlock('text', 'Hello world');
        $headingBlock = $this->richContentService->createBlock('heading', 'Title');
        $richContent['blocks'] = [$textBlock, $headingBlock];
        
        $html = $this->richContentService->exportToHtml($richContent);
        
        $this->assertStringContainsString('Hello world', $html);
        $this->assertStringContainsString('Title', $html);
    }

    /** @test */
    public function it_exports_to_markdown()
    {
        $richContent = $this->richContentService->createFromContent('');
        $textBlock = $this->richContentService->createBlock('text', 'Hello world');
        $headingBlock = $this->richContentService->createBlock('heading', 'Title');
        $richContent['blocks'] = [$textBlock, $headingBlock];
        
        $markdown = $this->richContentService->exportToMarkdown($richContent);
        
        $this->assertStringContainsString('Hello world', $markdown);
        $this->assertStringContainsString('# Title', $markdown);
    }

    /** @test */
    public function it_handles_invalid_blocks()
    {
        $richContent = [
            'type' => 'html',
            'data' => '',
            'blocks' => [
                ['invalid' => 'block'],
                [
                    'id' => 'valid',
                    'type' => 'text',
                    'content' => 'Valid content',
                    'metadata' => []
                ]
            ]
        ];
        
        $result = $this->richContentService->processRichContent($richContent);
        
        // Должен оставить только валидный блок
        $this->assertCount(1, $result['blocks']);
        $this->assertEquals('Valid content', $result['blocks'][0]['content']);
    }

    /** @test */
    public function it_handles_empty_rich_content()
    {
        $result = $this->richContentService->processRichContent([]);
        
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('blocks', $result);
        $this->assertIsArray($result['blocks']);
    }
}
