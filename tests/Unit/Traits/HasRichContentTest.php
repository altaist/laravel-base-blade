<?php

namespace Tests\Unit\Traits;

use App\Models\Article;
use App\Services\Content\RichContentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasRichContentTest extends TestCase
{
    use RefreshDatabase;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mock(RichContentService::class);
        
        $this->article = Article::factory()->create([
            'rich_content' => [
                'type' => 'html',
                'data' => '',
                'blocks' => [
                    [
                        'id' => 'block1',
                        'type' => 'text',
                        'content' => 'First block',
                        'metadata' => []
                    ],
                    [
                        'id' => 'block2',
                        'type' => 'heading',
                        'content' => 'Second block',
                        'metadata' => []
                    ]
                ],
                'metadata' => [
                    'word_count' => 4,
                    'reading_time' => 1,
                    'last_processed' => now(),
                    'version' => '1.0'
                ]
            ]
        ]);
    }

    /** @test */
    public function it_gets_rich_content()
    {
        $richContent = $this->article->getRichContent();

        $this->assertArrayHasKey('type', $richContent);
        $this->assertArrayHasKey('blocks', $richContent);
        $this->assertArrayHasKey('metadata', $richContent);
        $this->assertEquals('html', $richContent['type']);
    }

    /** @test */
    public function it_sets_rich_content()
    {
        $newRichContent = [
            'type' => 'html',
            'data' => '',
            'blocks' => [
                [
                    'id' => 'new_block',
                    'type' => 'text',
                    'content' => 'New content',
                    'metadata' => []
                ]
            ],
            'metadata' => [
                'word_count' => 2,
                'reading_time' => 1,
                'last_processed' => now(),
                'version' => '1.0'
            ]
        ];

        $this->article->setRichContent($newRichContent);

        $this->assertEquals($newRichContent, $this->article->rich_content);
    }

    /** @test */
    public function it_checks_if_has_rich_content_blocks()
    {
        $this->assertTrue($this->article->hasRichContentBlocks());

        $emptyArticle = Article::factory()->create([
            'rich_content' => [
                'type' => 'html',
                'data' => '',
                'blocks' => [],
                'metadata' => []
            ]
        ]);

        $this->assertFalse($emptyArticle->hasRichContentBlocks());
    }

    /** @test */
    public function it_gets_rich_content_blocks()
    {
        $blocks = $this->article->getRichContentBlocks();

        $this->assertCount(2, $blocks);
        $this->assertEquals('block1', $blocks[0]['id']);
        $this->assertEquals('block2', $blocks[1]['id']);
    }

    /** @test */
    public function it_adds_rich_content_block()
    {
        $this->article->addRichContentBlock('text', 'New block content', ['custom' => 'metadata']);

        $blocks = $this->article->getRichContentBlocks();
        $this->assertCount(3, $blocks);
        
        $lastBlock = end($blocks);
        $this->assertEquals('text', $lastBlock['type']);
        $this->assertEquals('New block content', $lastBlock['content']);
    }

    /** @test */
    public function it_removes_rich_content_block()
    {
        $this->article->removeRichContentBlock('block1');

        $blocks = $this->article->getRichContentBlocks();
        $this->assertCount(1, $blocks);
        $this->assertEquals('block2', $blocks[0]['id']);
    }

    /** @test */
    public function it_gets_rich_content_as_html()
    {
        $html = $this->article->getRichContentAsHtml();

        $this->assertStringContainsString('First block', $html);
        $this->assertStringContainsString('Second block', $html);
    }

    /** @test */
    public function it_gets_rich_content_as_markdown()
    {
        $markdown = $this->article->getRichContentAsMarkdown();

        $this->assertStringContainsString('First block', $markdown);
        $this->assertStringContainsString('Second block', $markdown);
    }

    /** @test */
    public function it_gets_rich_content_stats()
    {
        $stats = $this->article->getRichContentStats();

        $this->assertArrayHasKey('block_count', $stats);
        $this->assertArrayHasKey('word_count', $stats);
        $this->assertArrayHasKey('reading_time', $stats);
        $this->assertEquals(2, $stats['block_count']);
    }

    /** @test */
    public function it_syncs_rich_content_with_content()
    {
        $this->article->syncRichContentWithContent();

        // Проверяем, что контент был синхронизирован
        $this->assertNotEmpty($this->article->content);
    }

    /** @test */
    public function it_handles_empty_rich_content()
    {
        $emptyArticle = Article::factory()->create([
            'rich_content' => null
        ]);

        $richContent = $emptyArticle->getRichContent();
        $this->assertArrayHasKey('type', $richContent);
        $this->assertArrayHasKey('blocks', $richContent);

        $this->assertFalse($emptyArticle->hasRichContentBlocks());
        $this->assertCount(0, $emptyArticle->getRichContentBlocks());
    }

    /** @test */
    public function it_handles_invalid_block_removal()
    {
        $initialBlocks = $this->article->getRichContentBlocks();
        
        $this->article->removeRichContentBlock('non_existent_block');
        
        $this->assertEquals($initialBlocks, $this->article->getRichContentBlocks());
    }
}
