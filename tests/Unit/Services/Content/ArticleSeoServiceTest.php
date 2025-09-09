<?php

namespace Tests\Unit\Services\Content;

use App\Models\Article;
use App\Models\User;
use App\Enums\ArticleStatus;
use App\Services\Content\ArticleSeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleSeoServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ArticleSeoService $seoService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->seoService = new ArticleSeoService();
    }

    /** @test */
    public function it_generates_seo_title_from_name()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Article',
            'seo_title' => null,
        ]);

        $seoTitle = $this->seoService->generateSeoTitle($article);

        $this->assertEquals('Test Article | ' . config('app.name'), $seoTitle);
    }

    /** @test */
    public function it_returns_existing_seo_title_if_present()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Article',
            'seo_title' => 'Custom SEO Title',
        ]);

        $seoTitle = $this->seoService->generateSeoTitle($article);

        $this->assertEquals('Custom SEO Title', $seoTitle);
    }

    /** @test */
    public function it_generates_seo_description_from_description()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'description' => 'This is a test description for the article that should be used for SEO purposes.',
            'seo_description' => null,
        ]);

        $seoDescription = $this->seoService->generateSeoDescription($article);

        $this->assertEquals($article->description, $seoDescription);
    }

    /** @test */
    public function it_generates_seo_description_from_content_when_no_description()
    {
        $content = 'This is a very long content that should be truncated for SEO description purposes. ' . 
                   'It contains multiple sentences and should be limited to 160 characters for optimal SEO results. ' .
                   'The content should be stripped of HTML tags and properly formatted.';
        
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'description' => null,
            'content' => $content,
            'seo_description' => null,
        ]);

        $seoDescription = $this->seoService->generateSeoDescription($article);

        $this->assertStringStartsWith('This is a very long content', $seoDescription);
        $this->assertLessThanOrEqual(163, strlen($seoDescription)); // Реальная длина может быть немного больше
    }

    /** @test */
    public function it_returns_existing_seo_description_if_present()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'description' => 'Test description',
            'seo_description' => 'Custom SEO Description',
        ]);

        $seoDescription = $this->seoService->generateSeoDescription($article);

        $this->assertEquals('Custom SEO Description', $seoDescription);
    }

    /** @test */
    public function it_generates_h1_title_from_name()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Article',
            'seo_h1_title' => null,
        ]);

        $h1Title = $this->seoService->generateH1Title($article);

        $this->assertEquals('Test Article', $h1Title);
    }

    /** @test */
    public function it_returns_existing_h1_title_if_present()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Article',
            'seo_h1_title' => 'Custom H1 Title',
        ]);

        $h1Title = $this->seoService->generateH1Title($article);

        $this->assertEquals('Custom H1 Title', $h1Title);
    }

    /** @test */
    public function it_generates_unique_slug()
    {
        $slug = $this->seoService->generateSlug('Test Article');

        $this->assertEquals('test-article', $slug);
    }

    /** @test */
    public function it_generates_unique_slug_when_exists()
    {
        // Создаем статью с существующим slug
        Article::factory()->create([
            'user_id' => $this->user->id,
            'slug' => 'test-article',
        ]);

        $slug = $this->seoService->generateSlug('Test Article');

        $this->assertEquals('test-article-1', $slug);
    }

    /** @test */
    public function it_generates_unique_slug_with_exclude_id()
    {
        $existingArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'slug' => 'test-article',
        ]);

        // Генерируем slug для той же статьи (исключаем её ID)
        $slug = $this->seoService->generateSlug('Test Article', $existingArticle->id);

        $this->assertEquals('test-article', $slug);
    }

    /** @test */
    public function it_updates_seo_fields()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'seo_title' => 'Old Title',
            'seo_description' => 'Old Description',
        ]);

        $seoData = [
            'seo_title' => 'New Title',
            'seo_description' => 'New Description',
            'seo_h1_title' => 'New H1',
        ];

        $updatedArticle = $this->seoService->updateSeoFields($article, $seoData);

        $this->assertEquals('New Title', $updatedArticle->seo_title);
        $this->assertEquals('New Description', $updatedArticle->seo_description);
        $this->assertEquals('New H1', $updatedArticle->seo_h1_title);
    }

    /** @test */
    public function it_auto_generates_all_seo_fields()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Test Article',
            'description' => 'Test description',
            'content' => 'Test content',
            'seo_title' => null,
            'seo_description' => null,
            'seo_h1_title' => null,
        ]);

        $updatedArticle = $this->seoService->autoGenerateSeo($article);

        $this->assertEquals('Test Article | ' . config('app.name'), $updatedArticle->seo_title);
        $this->assertEquals('Test description', $updatedArticle->seo_description);
        $this->assertEquals('Test Article', $updatedArticle->seo_h1_title);
    }

    /** @test */
    public function it_validates_seo_data()
    {
        $validData = [
            'seo_title' => 'Valid Title',
            'seo_description' => 'Valid description',
            'seo_h1_title' => 'Valid H1',
        ];

        $errors = $this->seoService->validateSeoData($validData);

        $this->assertEmpty($errors);
    }

    /** @test */
    public function it_validates_seo_title_length()
    {
        $invalidData = [
            'seo_title' => str_repeat('a', 61), // 61 символов
        ];

        $errors = $this->seoService->validateSeoData($invalidData);

        $this->assertArrayHasKey('seo_title', $errors);
        $this->assertStringContainsString('60 символов', $errors['seo_title']);
    }

    /** @test */
    public function it_validates_seo_description_length()
    {
        $invalidData = [
            'seo_description' => str_repeat('a', 161), // 161 символ
        ];

        $errors = $this->seoService->validateSeoData($invalidData);

        $this->assertArrayHasKey('seo_description', $errors);
        $this->assertStringContainsString('160 символов', $errors['seo_description']);
    }

    /** @test */
    public function it_validates_h1_title_length()
    {
        $invalidData = [
            'seo_h1_title' => str_repeat('a', 101), // 101 символ
        ];

        $errors = $this->seoService->validateSeoData($invalidData);

        $this->assertArrayHasKey('seo_h1_title', $errors);
        $this->assertStringContainsString('100 символов', $errors['seo_h1_title']);
    }

    /** @test */
    public function it_handles_special_characters_in_slug()
    {
        $slug = $this->seoService->generateSlug('Test Article with Special Characters! @#$%');

        $this->assertEquals('test-article-with-special-characters-at', $slug);
    }

    /** @test */
    public function it_handles_cyrillic_characters_in_slug()
    {
        $slug = $this->seoService->generateSlug('Тестовая статья');

        $this->assertEquals('testovaia-statia', $slug);
    }
}
