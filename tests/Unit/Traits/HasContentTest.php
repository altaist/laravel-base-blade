<?php

namespace Tests\Unit\Traits;

use App\Models\Article;
use App\Services\Content\ContentService;
use App\Services\Content\RichContentService;
use App\Services\Content\SeoService;
use App\Services\Content\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasContentTest extends TestCase
{
    use RefreshDatabase;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Мокаем сервисы
        $this->mock(ContentService::class);
        $this->mock(RichContentService::class);
        $this->mock(SeoService::class);
        $this->mock(MediaService::class);
        
        $this->article = Article::factory()->create([
            'name' => 'Test Article',
            'description' => 'Test Description',
            'content' => '<p>Test content</p>',
            'rich_content' => [
                'type' => 'html',
                'data' => '',
                'blocks' => [
                    [
                        'id' => 'block1',
                        'type' => 'text',
                        'content' => 'Rich content block',
                        'metadata' => []
                    ]
                ],
                'metadata' => [
                    'word_count' => 3,
                    'reading_time' => 1,
                    'last_processed' => now(),
                    'version' => '1.0'
                ]
            ]
        ]);
    }

    /** @test */
    public function it_gets_content_field_by_type()
    {
        $title = $this->article->getContentField('title');
        $summary = $this->article->getContentField('summary');
        $body = $this->article->getContentField('body');

        $this->assertEquals('Test Article', $title);
        $this->assertEquals('Test Description', $summary);
        $this->assertEquals('<p>Test content</p>', $body);
    }

    /** @test */
    public function it_gets_content_field_in_different_formats()
    {
        $htmlContent = $this->article->getContentField('body', 'html');
        $textContent = $this->article->getContentField('body', 'text');
        $rawContent = $this->article->getContentField('body', 'raw');

        $this->assertStringContainsString('<p>', $htmlContent);
        $this->assertStringNotContainsString('<p>', $textContent);
        $this->assertEquals('<p>Test content</p>', $rawContent);
    }

    /** @test */
    public function it_sets_content_field()
    {
        $this->article->setContentField('title', 'New Title');

        $this->assertEquals('New Title', $this->article->name);
    }

    /** @test */
    public function it_checks_if_has_rich_content()
    {
        $this->assertTrue($this->article->hasRichContent());

        $articleWithoutRich = Article::factory()->create([
            'rich_content' => null
        ]);

        $this->assertFalse($articleWithoutRich->hasRichContent());
    }

    /** @test */
    public function it_gets_list_content()
    {
        $listContent = $this->article->getListContent();

        $this->assertStringContainsString('Test Article', $listContent);
        $this->assertStringContainsString('Test Description', $listContent);
    }

    /** @test */
    public function it_gets_content_stats()
    {
        $stats = $this->article->getContentStats();

        $this->assertArrayHasKey('word_count', $stats);
        $this->assertArrayHasKey('reading_time', $stats);
        $this->assertArrayHasKey('character_count', $stats);
        $this->assertArrayHasKey('has_rich_content', $stats);
    }

    /** @test */
    public function it_returns_null_for_invalid_content_type()
    {
        $result = $this->article->getContentField('invalid_type');

        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_empty_content_fields()
    {
        $emptyArticle = Article::factory()->create([
            'name' => '',
            'description' => null,
            'content' => ''
        ]);

        $title = $emptyArticle->getContentField('title');
        $summary = $emptyArticle->getContentField('summary');
        $body = $emptyArticle->getContentField('body');

        $this->assertEquals('', $title);
        $this->assertNull($summary);
        $this->assertEquals('', $body);
    }
}
