<?php

namespace Tests\Unit\Services\Content;

use App\Models\Article;
use App\Models\User;
use App\Models\File;
use App\Enums\ArticleStatus;
use App\Services\Content\ArticleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticleServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ArticleService $articleService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->articleService = new ArticleService();
    }

    /** @test */
    public function it_can_create_article()
    {
        $data = [
            'user_id' => $this->user->id,
            'name' => 'Test Article',
            'slug' => 'test-article',
            'description' => 'Test description',
            'content' => 'Test content',
            'status' => ArticleStatus::DRAFT->value,
        ];

        $article = $this->articleService->create($data);

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals($data['name'], $article->name);
        $this->assertEquals($data['slug'], $article->slug);
        $this->assertEquals($data['description'], $article->description);
        $this->assertEquals($data['content'], $article->content);
        $this->assertEquals($data['user_id'], $article->user_id);
        $this->assertDatabaseHas('articles', [
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);
    }

    /** @test */
    public function it_can_update_article()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
            'slug' => 'original-slug',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'slug' => 'updated-slug',
            'description' => 'Updated description',
            'content' => 'Updated content',
        ];

        $updatedArticle = $this->articleService->update($article, $updateData);

        $this->assertEquals($updateData['name'], $updatedArticle->name);
        $this->assertEquals($updateData['slug'], $updatedArticle->slug);
        $this->assertEquals($updateData['description'], $updatedArticle->description);
        $this->assertEquals($updateData['content'], $updatedArticle->content);
        
        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'name' => $updateData['name'],
            'slug' => $updateData['slug'],
        ]);
    }

    /** @test */
    public function it_can_delete_article()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $result = $this->articleService->delete($article);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }

    /** @test */
    public function it_can_get_published_articles()
    {
        // Создаем статьи с разными статусами
        Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::PUBLISHED,
        ]);
        Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::DRAFT,
        ]);
        Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::READY_TO_PUBLISH,
        ]);

        $publishedArticles = $this->articleService->getPublished();

        $this->assertCount(1, $publishedArticles);
        $this->assertEquals(ArticleStatus::PUBLISHED, $publishedArticles->first()->status);
    }

    /** @test */
    public function it_can_get_articles_by_status()
    {
        // Создаем статьи с разными статусами
        Article::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::DRAFT,
        ]);
        Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::PUBLISHED,
        ]);

        $draftArticles = $this->articleService->getByStatus(ArticleStatus::DRAFT->value);

        $this->assertCount(2, $draftArticles);
        foreach ($draftArticles as $article) {
            $this->assertEquals(ArticleStatus::DRAFT, $article->status);
        }
    }

    /** @test */
    public function it_can_get_articles_by_user()
    {
        $anotherUser = User::factory()->create();
        
        // Создаем статьи для разных пользователей
        Article::factory()->count(2)->create([
            'user_id' => $this->user->id,
        ]);
        Article::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $userArticles = $this->articleService->getByUser($this->user);

        $this->assertCount(2, $userArticles);
        foreach ($userArticles as $article) {
            $this->assertEquals($this->user->id, $article->user_id);
        }
    }

    /** @test */
    public function it_can_find_article_by_slug()
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'slug' => 'unique-slug',
        ]);

        $foundArticle = $this->articleService->findBySlug('unique-slug');

        $this->assertInstanceOf(Article::class, $foundArticle);
        $this->assertEquals($article->id, $foundArticle->id);
        $this->assertEquals('unique-slug', $foundArticle->slug);
    }

    /** @test */
    public function it_returns_null_for_non_existent_slug()
    {
        $foundArticle = $this->articleService->findBySlug('non-existent-slug');

        $this->assertNull($foundArticle);
    }

    /** @test */
    public function it_can_get_count_by_status()
    {
        // Создаем статьи с разными статусами
        Article::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::DRAFT,
        ]);
        Article::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::PUBLISHED,
        ]);
        Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::READY_TO_PUBLISH,
        ]);

        $counts = $this->articleService->getCountByStatus();

        $this->assertIsArray($counts);
        $this->assertEquals(3, $counts[ArticleStatus::DRAFT->value]);
        $this->assertEquals(2, $counts[ArticleStatus::PUBLISHED->value]);
        $this->assertEquals(1, $counts[ArticleStatus::READY_TO_PUBLISH->value]);
    }

    /** @test */
    public function it_can_get_popular_articles()
    {
        // Создаем статьи с разными статусами
        $publishedArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::PUBLISHED,
        ]);
        Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::DRAFT,
        ]);

        $popularArticles = $this->articleService->getPopularArticles(10);

        $this->assertCount(1, $popularArticles);
        $this->assertEquals($publishedArticle->id, $popularArticles->first()->id);
    }

    /** @test */
    public function it_respects_limit_for_popular_articles()
    {
        // Создаем 5 опубликованных статей
        Article::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::PUBLISHED,
        ]);

        $popularArticles = $this->articleService->getPopularArticles(3);

        $this->assertCount(3, $popularArticles);
    }

    /** @test */
    public function it_can_create_article_with_image()
    {
        $file = File::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'user_id' => $this->user->id,
            'name' => 'Article with Image',
            'slug' => 'article-with-image',
            'content' => 'Content with image',
            'img_file_id' => $file->id,
        ];

        $article = $this->articleService->create($data);

        $this->assertEquals($file->id, $article->img_file_id);
        $this->assertDatabaseHas('articles', [
            'img_file_id' => $file->id,
        ]);
    }
}
