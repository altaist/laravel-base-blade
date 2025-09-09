<?php

namespace Tests\Unit\Services;

use App\Models\Article;
use App\Models\User;
use App\Enums\ArticleStatus;
use App\Services\StatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class StatusServiceTest extends TestCase
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
    public function it_can_change_status_for_model()
    {
        $updatedArticle = StatusService::changeStatusFor($this->article, ArticleStatus::PUBLISHED->value);
        
        $this->assertEquals(ArticleStatus::PUBLISHED, $updatedArticle->status);
        $this->assertEquals(ArticleStatus::PUBLISHED, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_publish_model()
    {
        $updatedArticle = StatusService::publishFor($this->article);
        
        $this->assertEquals(ArticleStatus::PUBLISHED, $updatedArticle->status);
        $this->assertEquals(ArticleStatus::PUBLISHED, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_unpublish_model()
    {
        // Сначала публикуем
        $this->article->update(['status' => ArticleStatus::PUBLISHED->value]);
        
        $updatedArticle = StatusService::unpublishFor($this->article);
        
        $this->assertEquals(ArticleStatus::DRAFT, $updatedArticle->status);
        $this->assertEquals(ArticleStatus::DRAFT, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_mark_as_ready()
    {
        $updatedArticle = StatusService::markAsReadyFor($this->article);
        
        $this->assertEquals(ArticleStatus::READY_TO_PUBLISH, $updatedArticle->status);
        $this->assertEquals(ArticleStatus::READY_TO_PUBLISH, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_mark_as_draft()
    {
        // Сначала публикуем
        $this->article->update(['status' => ArticleStatus::PUBLISHED->value]);
        
        $updatedArticle = StatusService::markAsDraftFor($this->article);
        
        $this->assertEquals(ArticleStatus::DRAFT, $updatedArticle->status);
        $this->assertEquals(ArticleStatus::DRAFT, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_check_if_status_can_be_changed()
    {
        // Проверяем валидные статусы
        $this->assertTrue(StatusService::canChangeStatusFor($this->article, ArticleStatus::DRAFT->value));
        $this->assertTrue(StatusService::canChangeStatusFor($this->article, ArticleStatus::READY_TO_PUBLISH->value));
        $this->assertTrue(StatusService::canChangeStatusFor($this->article, ArticleStatus::PUBLISHED->value));
        
        // Проверяем невалидный статус
        $this->assertFalse(StatusService::canChangeStatusFor($this->article, 'invalid_status'));
    }

    /** @test */
    public function it_can_get_available_statuses_for_article()
    {
        $availableStatuses = StatusService::getAvailableStatusesFor($this->article);
        
        $expectedStatuses = [
            ArticleStatus::DRAFT->value,
            ArticleStatus::READY_TO_PUBLISH->value,
            ArticleStatus::PUBLISHED->value,
        ];
        
        $this->assertEquals($expectedStatuses, $availableStatuses);
    }

    /** @test */
    public function it_throws_exception_for_invalid_status_change()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot change status to invalid_status');
        
        StatusService::changeStatusFor($this->article, 'invalid_status');
    }

    /** @test */
    public function it_returns_fresh_model_instance()
    {
        $updatedArticle = StatusService::publishFor($this->article);
        
        $this->assertInstanceOf(Article::class, $updatedArticle);
        $this->assertEquals($this->article->id, $updatedArticle->id);
        $this->assertNotSame($this->article, $updatedArticle); // Должен быть новый экземпляр
    }

    /** @test */
    public function it_can_bulk_change_status()
    {
        // Создаем несколько статей
        $articles = Article::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => ArticleStatus::DRAFT,
        ]);
        
        $updatedArticles = StatusService::bulkChangeStatus($articles, ArticleStatus::PUBLISHED->value);
        
        $this->assertInstanceOf(Collection::class, $updatedArticles);
        $this->assertCount(3, $updatedArticles);
        
        foreach ($updatedArticles as $article) {
            $this->assertEquals(ArticleStatus::PUBLISHED, $article->status);
        }
        
        // Проверяем, что в базе данных тоже обновилось
        foreach ($articles as $article) {
            $this->assertEquals(ArticleStatus::PUBLISHED, $article->fresh()->status);
        }
    }

    /** @test */
    public function it_returns_empty_array_for_unknown_model_type()
    {
        // Создаем модель без статуса (например, User)
        $availableStatuses = StatusService::getAvailableStatusesFor($this->user);
        
        $this->assertEquals([], $availableStatuses);
    }

    /** @test */
    public function it_returns_false_for_model_without_status_column()
    {
        // User модель не имеет колонку status
        $canChange = StatusService::canChangeStatusFor($this->user, ArticleStatus::PUBLISHED->value);
        
        $this->assertFalse($canChange);
    }
}
