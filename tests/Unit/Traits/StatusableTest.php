<?php

namespace Tests\Unit\Traits;

use App\Models\Article;
use App\Models\User;
use App\Enums\ArticleStatus;
use App\Services\StatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusableTest extends TestCase
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
    public function it_can_change_status()
    {
        $this->article->changeStatus(ArticleStatus::PUBLISHED->value);
        
        $this->assertEquals(ArticleStatus::PUBLISHED, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_publish_article()
    {
        $this->article->publish();
        
        $this->assertEquals(ArticleStatus::PUBLISHED, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_unpublish_article()
    {
        // Сначала публикуем
        $this->article->publish();
        $this->assertEquals(ArticleStatus::PUBLISHED, $this->article->fresh()->status);
        
        // Затем снимаем с публикации
        $this->article->unpublish();
        
        $this->assertEquals(ArticleStatus::DRAFT, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_mark_as_ready()
    {
        $this->article->markAsReady();
        
        $this->assertEquals(ArticleStatus::READY_TO_PUBLISH, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_mark_as_draft()
    {
        // Сначала публикуем
        $this->article->publish();
        $this->assertEquals(ArticleStatus::PUBLISHED, $this->article->fresh()->status);
        
        // Затем возвращаем в черновики
        $this->article->markAsDraft();
        
        $this->assertEquals(ArticleStatus::DRAFT, $this->article->fresh()->status);
    }

    /** @test */
    public function it_can_check_if_status_can_be_changed()
    {
        // Проверяем валидные переходы
        $this->assertTrue($this->article->canChangeStatusTo(ArticleStatus::READY_TO_PUBLISH->value));
        $this->assertTrue($this->article->canChangeStatusTo(ArticleStatus::PUBLISHED->value));
        
        // Проверяем невалидные переходы (если есть ограничения)
        $this->assertTrue($this->article->canChangeStatusTo(ArticleStatus::DRAFT->value));
    }

    /** @test */
    public function it_can_get_available_statuses()
    {
        $availableStatuses = $this->article->getAvailableStatuses();
        
        $expectedStatuses = [
            ArticleStatus::DRAFT->value,
            ArticleStatus::READY_TO_PUBLISH->value,
            ArticleStatus::PUBLISHED->value,
        ];
        
        $this->assertEquals($expectedStatuses, $availableStatuses);
    }

    /** @test */
    public function it_throws_exception_for_invalid_status()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->article->changeStatus('invalid_status');
    }

    /** @test */
    public function it_returns_updated_model_instance()
    {
        $updatedArticle = $this->article->publish();
        
        $this->assertInstanceOf(Article::class, $updatedArticle);
        $this->assertEquals(ArticleStatus::PUBLISHED, $updatedArticle->status);
        $this->assertEquals($this->article->id, $updatedArticle->id);
    }

    /** @test */
    public function it_works_with_different_status_transitions()
    {
        // Draft -> Ready to Publish
        $this->article->markAsReady();
        $this->assertEquals(ArticleStatus::READY_TO_PUBLISH, $this->article->fresh()->status);
        
        // Ready to Publish -> Published
        $this->article->publish();
        $this->assertEquals(ArticleStatus::PUBLISHED, $this->article->fresh()->status);
        
        // Published -> Draft
        $this->article->markAsDraft();
        $this->assertEquals(ArticleStatus::DRAFT, $this->article->fresh()->status);
    }
}
