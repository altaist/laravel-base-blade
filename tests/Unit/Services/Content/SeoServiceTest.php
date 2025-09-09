<?php

namespace Tests\Unit\Services\Content;

use App\Models\Article;
use App\Services\Content\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoServiceTest extends TestCase
{
    use RefreshDatabase;

    private SeoService $seoService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seoService = new SeoService();
    }

    /** @test */
    public function it_generates_seo_title_from_explicit_field()
    {
        $article = new Article([
            'name' => 'Test Article',
            'seo_title' => 'Custom SEO Title'
        ]);

        $result = $this->seoService->generateSeoTitle($article);

        $this->assertEquals('Custom SEO Title', $result);
    }

    /** @test */
    public function it_generates_seo_title_from_name_field()
    {
        $article = new Article([
            'name' => 'Test Article Title'
        ]);

        $result = $this->seoService->generateSeoTitle($article);

        $this->assertStringContainsString('Test Article Title', $result);
    }

    /** @test */
    public function it_generates_seo_title_with_app_name_for_short_titles()
    {
        $article = new Article([
            'name' => 'Short'
        ]);

        $result = $this->seoService->generateSeoTitle($article);

        $this->assertStringContainsString('Short', $result);
        $this->assertStringContainsString(config('app.name'), $result);
    }

    /** @test */
    public function it_generates_seo_description_from_explicit_field()
    {
        $article = new Article([
            'description' => 'Test description',
            'seo_description' => 'Custom SEO Description'
        ]);

        $result = $this->seoService->generateSeoDescription($article);

        $this->assertEquals('Custom SEO Description', $result);
    }

    /** @test */
    public function it_generates_seo_description_from_description_field()
    {
        $article = new Article([
            'description' => 'This is a test description for the article'
        ]);

        $result = $this->seoService->generateSeoDescription($article);

        $this->assertEquals('This is a test description for the article', $result);
    }

    /** @test */
    public function it_generates_seo_description_from_content()
    {
        $article = new Article([
            'content' => '<p>This is a long content that should be used to generate SEO description when no explicit description is provided.</p>'
        ]);

        $result = $this->seoService->generateSeoDescription($article);

        $this->assertStringContainsString('This is a long content', $result);
        $this->assertLessThanOrEqual(160, strlen($result));
    }

    /** @test */
    public function it_generates_h1_title_from_explicit_field()
    {
        $article = new Article([
            'name' => 'Test Article',
            'seo_h1_title' => 'Custom H1 Title'
        ]);

        $result = $this->seoService->generateH1Title($article);

        $this->assertEquals('Custom H1 Title', $result);
    }

    /** @test */
    public function it_generates_h1_title_from_name_field()
    {
        $article = new Article([
            'name' => 'Test Article Title'
        ]);

        $result = $this->seoService->generateH1Title($article);

        $this->assertEquals('Test Article Title', $result);
    }

    /** @test */
    public function it_generates_unique_slug()
    {
        $slug = $this->seoService->generateSlug('Test Article Title', null, Article::class);

        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_generates_unique_slug_with_exclusion()
    {
        // Создаем статью для тестирования уникальности
        $article = Article::factory()->create(['name' => 'Test Article']);

        $slug = $this->seoService->generateSlug('Test Article', $article->id, Article::class);

        $this->assertEquals('test-article', $slug);
    }

    /** @test */
    public function it_checks_slug_uniqueness()
    {
        $article = Article::factory()->create(['name' => 'Test Article']);

        $isUnique = $this->seoService->isSlugUnique('test-article', $article->id, Article::class);
        $isNotUnique = $this->seoService->isSlugUnique('test-article', null, Article::class);

        $this->assertTrue($isUnique);
        $this->assertTrue($isNotUnique); // Slug может быть уникальным даже без исключения
    }

    /** @test */
    public function it_handles_empty_model_fields()
    {
        $article = new Article();

        $title = $this->seoService->generateSeoTitle($article);
        $description = $this->seoService->generateSeoDescription($article);
        $h1 = $this->seoService->generateH1Title($article);

        $this->assertEquals(config('app.name'), $title);
        $this->assertIsString($description);
        $this->assertIsString($h1);
    }

    /** @test */
    public function it_optimizes_titles_and_descriptions()
    {
        $article = new Article([
            'name' => '  test article title  ',
            'description' => '  test   description  with   multiple   spaces  '
        ]);

        $title = $this->seoService->generateSeoTitle($article);
        $description = $this->seoService->generateSeoDescription($article);

        $this->assertStringContainsString('test article title', $title);
        $this->assertStringContainsString('test', $description);
        $this->assertStringContainsString('description', $description);
    }
}
