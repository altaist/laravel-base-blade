<?php

namespace Tests\Unit\Services\Reactions;

use App\Models\Article;
use App\Models\User;
use App\Services\Reactions\LikeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeServiceTest extends TestCase
{
    use RefreshDatabase;

    private LikeService $likeService;
    private User $user;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->likeService = new LikeService();
        $this->user = User::factory()->create();
        $this->article = Article::factory()->create();
    }

    public function test_can_like_article()
    {
        $like = $this->likeService->like($this->user, $this->article);

        $this->assertNotNull($like);
        $this->assertEquals($this->user->id, $like->user_id);
        $this->assertEquals(Article::class, $like->likeable_type);
        $this->assertEquals($this->article->id, $like->likeable_id);
    }

    public function test_can_unlike_article()
    {
        // Сначала лайкаем
        $this->likeService->like($this->user, $this->article);

        // Потом убираем лайк
        $result = $this->likeService->unlike($this->user, $this->article);

        $this->assertTrue($result);
        $this->assertFalse($this->likeService->isLiked($this->user, $this->article));
    }

    public function test_can_toggle_like()
    {
        // Первый toggle - лайк
        $result = $this->likeService->toggleLike($this->user, $this->article);
        $this->assertTrue($result);
        $this->assertTrue($this->likeService->isLiked($this->user, $this->article));

        // Второй toggle - убираем лайк
        $result = $this->likeService->toggleLike($this->user, $this->article);
        $this->assertFalse($result);
        $this->assertFalse($this->likeService->isLiked($this->user, $this->article));
    }

    public function test_can_count_likes()
    {
        // Изначально 0 лайков
        $this->assertEquals(0, $this->likeService->getLikesCount($this->article));

        // Добавляем лайк
        $this->likeService->like($this->user, $this->article);
        $this->assertEquals(1, $this->likeService->getLikesCount($this->article));
    }
}
