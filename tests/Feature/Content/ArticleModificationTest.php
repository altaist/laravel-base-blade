<?php

namespace Tests\Feature\Content;

use App\Models\Article;
use App\Models\User;
use App\Models\File;
use App\Enums\ArticleStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleModificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->article = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::DRAFT,
        ]);
    }

    /** @test */
    public function it_can_create_article_via_api()
    {
        $this->actingAs($this->user);

        $data = [
            'name' => 'New Article',
            'description' => 'Article description',
            'content' => 'Article content',
            'seo_title' => 'SEO Title',
            'seo_description' => 'SEO Description',
            'seo_h1_title' => 'H1 Title',
        ];

        $response = $this->postJson('/api/articles', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'article' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'content',
                    'seo_title',
                    'seo_description',
                    'seo_h1_title',
                    'user_id',
                    'created_at',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('articles', [
            'name' => $data['name'],
            'description' => $data['description'],
            'content' => $data['content'],
        ]);
    }

    /** @test */
    public function it_can_update_article_via_api()
    {
        $this->actingAs($this->user);

        $updateData = [
            'name' => 'Updated Article',
            'description' => 'Updated description',
            'content' => 'Updated content',
            'seo_title' => 'Updated SEO Title',
        ];

        $response = $this->putJson("/api/articles/{$this->article->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'article' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'content',
                    'seo_title',
                    'updated_at',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'name' => $updateData['name'],
            'description' => $updateData['description'],
        ]);
    }

    /** @test */
    public function it_can_delete_article_via_api()
    {
        $this->actingAs($this->user);

        $response = $this->deleteJson("/api/articles/{$this->article->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Статья успешно удалена',
            ]);

        $this->assertDatabaseMissing('articles', [
            'id' => $this->article->id,
        ]);
    }

    /** @test */
    public function it_can_change_article_status_via_api()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/status/{$this->article->id}/change", [
            'status' => ArticleStatus::PUBLISHED->value,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'article' => [
                    'id',
                    'status',
                ],
                'message',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'status' => ArticleStatus::PUBLISHED->value,
        ]);
    }

    /** @test */
    public function it_can_publish_article_via_api()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/status/{$this->article->id}/publish");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Статья опубликована',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'status' => ArticleStatus::PUBLISHED->value,
        ]);
    }

    /** @test */
    public function it_can_unpublish_article_via_api()
    {
        // Сначала публикуем статью
        $this->article->update(['status' => ArticleStatus::PUBLISHED->value]);
        $this->actingAs($this->user);

        $response = $this->postJson("/api/status/{$this->article->id}/unpublish");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Статья снята с публикации',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'status' => ArticleStatus::DRAFT->value,
        ]);
    }

    /** @test */
    public function it_can_mark_article_as_ready_via_api()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/status/{$this->article->id}/ready");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Статья отмечена как готовая к публикации',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'status' => ArticleStatus::READY_TO_PUBLISH->value,
        ]);
    }

    /** @test */
    public function it_can_mark_article_as_draft_via_api()
    {
        // Сначала публикуем статью
        $this->article->update(['status' => ArticleStatus::PUBLISHED->value]);
        $this->actingAs($this->user);

        $response = $this->postJson("/api/status/{$this->article->id}/draft");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Статья возвращена в черновики',
            ]);

        $this->assertDatabaseHas('articles', [
            'id' => $this->article->id,
            'status' => ArticleStatus::DRAFT->value,
        ]);
    }

    /** @test */
    public function it_can_get_available_statuses_via_api()
    {
        $this->actingAs($this->user);

        $response = $this->getJson("/api/status/{$this->article->id}/available");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'current_status',
                'available_statuses',
            ]);

        $responseData = $response->json();
        $this->assertEquals(ArticleStatus::DRAFT->value, $responseData['current_status']);
        $this->assertContains(ArticleStatus::DRAFT->value, $responseData['available_statuses']);
        $this->assertContains(ArticleStatus::READY_TO_PUBLISH->value, $responseData['available_statuses']);
        $this->assertContains(ArticleStatus::PUBLISHED->value, $responseData['available_statuses']);
    }

    /** @test */
    public function it_requires_authentication_for_article_operations()
    {
        $response = $this->postJson('/api/articles', [
            'name' => 'Test Article',
            'content' => 'Test content',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_validates_required_fields_for_article_creation()
    {
        $this->actingAs($this->user);

        $response = $this->postJson('/api/articles', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'content']);
    }

    /** @test */
    public function it_validates_status_field_for_status_change()
    {
        $this->actingAs($this->user);

        $response = $this->postJson("/api/status/{$this->article->id}/change", [
            'status' => 'invalid_status',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function it_can_create_article_with_image()
    {
        $this->actingAs($this->user);
        
        $file = File::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = [
            'name' => 'Article with Image',
            'content' => 'Article content',
            'img_file_id' => $file->id,
        ];

        $response = $this->postJson('/api/articles', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('articles', [
            'name' => $data['name'],
            'img_file_id' => $file->id,
        ]);
    }

    /** @test */
    public function it_can_get_articles_list_via_api()
    {
        // Создаем несколько статей
        Article::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::PUBLISHED,
        ]);

        $this->actingAs($this->user);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'articles' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'status',
                        'created_at',
                    ],
                ],
                'count',
            ]);

        $responseData = $response->json();
        $this->assertEquals(3, $responseData['count']);
    }

    /** @test */
    public function it_can_get_single_article_via_api()
    {
        $this->actingAs($this->user);

        $response = $this->getJson("/api/articles/{$this->article->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'article' => [
                    'id',
                    'name',
                    'slug',
                    'description',
                    'content',
                    'user',
                ],
            ]);
    }
}
