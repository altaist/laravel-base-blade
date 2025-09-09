<?php

namespace Tests\Unit\Traits;

use App\Models\Article;
use App\Services\Content\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasSeoTest extends TestCase
{
    use RefreshDatabase;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mock(SeoService::class);
        
        $this->article = Article::factory()->create([
            'name' => 'Test Article',
            'description' => 'Test Description',
            'content' => '<p>Test content for SEO</p>',
            'seo_title' => 'Custom SEO Title',
            'seo_description' => 'Custom SEO Description',
            'seo_h1_title' => 'Custom H1 Title'
        ]);
    }

    /** @test */
    public function it_generates_seo_from_content()
    {
        $this->article->generateSeoFromContent();

        // Проверяем, что SEO поля были обновлены
        $this->assertNotEmpty($this->article->seo_title);
        $this->assertNotEmpty($this->article->seo_description);
        $this->assertNotEmpty($this->article->seo_h1_title);
    }

    /** @test */
    public function it_gets_seo_title()
    {
        $seoTitle = $this->article->getSeoTitle();

        $this->assertEquals('Custom SEO Title', $seoTitle);
    }

    /** @test */
    public function it_gets_seo_description()
    {
        $seoDescription = $this->article->getSeoDescription();

        $this->assertEquals('Custom SEO Description', $seoDescription);
    }

    /** @test */
    public function it_gets_seo_h1_title()
    {
        $seoH1 = $this->article->getSeoH1Title();

        $this->assertEquals('Custom H1 Title', $seoH1);
    }

    /** @test */
    public function it_gets_slug()
    {
        $slug = $this->article->getSlug();

        $this->assertIsString($slug);
        $this->assertNotEmpty($slug);
    }

    /** @test */
    public function it_updates_seo_fields()
    {
        $seoData = [
            'seo_title' => 'Updated SEO Title',
            'seo_description' => 'Updated SEO Description',
            'seo_h1_title' => 'Updated H1 Title'
        ];

        $this->article->updateSeoFields($seoData);

        $this->assertEquals('Updated SEO Title', $this->article->seo_title);
        $this->assertEquals('Updated SEO Description', $this->article->seo_description);
        $this->assertEquals('Updated H1 Title', $this->article->seo_h1_title);
    }

    /** @test */
    public function it_handles_partial_seo_updates()
    {
        $seoData = [
            'seo_title' => 'Only Title Updated'
        ];

        $originalDescription = $this->article->seo_description;
        $originalH1 = $this->article->seo_h1_title;

        $this->article->updateSeoFields($seoData);

        $this->assertEquals('Only Title Updated', $this->article->seo_title);
        $this->assertEquals($originalDescription, $this->article->seo_description);
        $this->assertEquals($originalH1, $this->article->seo_h1_title);
    }

    /** @test */
    public function it_handles_empty_seo_fields()
    {
        $emptyArticle = Article::factory()->create([
            'seo_title' => null,
            'seo_description' => null,
            'seo_h1_title' => null
        ]);

        $seoTitle = $emptyArticle->getSeoTitle();
        $seoDescription = $emptyArticle->getSeoDescription();
        $seoH1 = $emptyArticle->getSeoH1Title();

        $this->assertIsString($seoTitle);
        $this->assertIsString($seoDescription);
        $this->assertIsString($seoH1);
    }

    /** @test */
    public function it_generates_seo_when_fields_are_empty()
    {
        $articleWithoutSeo = Article::factory()->create([
            'name' => 'Article Without SEO',
            'description' => 'Article description',
            'content' => '<p>Article content</p>',
            'seo_title' => null,
            'seo_description' => null,
            'seo_h1_title' => null
        ]);

        $seoTitle = $articleWithoutSeo->getSeoTitle();
        $seoDescription = $articleWithoutSeo->getSeoDescription();
        $seoH1 = $articleWithoutSeo->getSeoH1Title();

        // Должны быть сгенерированы на основе контента
        $this->assertStringContainsString('Article Without SEO', $seoTitle);
        $this->assertStringContainsString('Article description', $seoDescription);
        $this->assertStringContainsString('Article Without SEO', $seoH1);
    }

    /** @test */
    public function it_handles_empty_content_for_seo_generation()
    {
        $emptyArticle = Article::factory()->create([
            'name' => '',
            'description' => null,
            'content' => '',
            'seo_title' => null,
            'seo_description' => null,
            'seo_h1_title' => null
        ]);

        $seoTitle = $emptyArticle->getSeoTitle();
        $seoDescription = $emptyArticle->getSeoDescription();
        $seoH1 = $emptyArticle->getSeoH1Title();

        // Должны вернуть значения по умолчанию
        $this->assertIsString($seoTitle);
        $this->assertIsString($seoDescription);
        $this->assertIsString($seoH1);
    }
}
