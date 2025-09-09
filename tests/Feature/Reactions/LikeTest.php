<?php

namespace Tests\Feature\Reactions;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->article = Article::factory()->create();
    }

    public function test_user_can_like_article()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/likes', [
                'likeable_type' => Article::class,
                'likeable_id' => $this->article->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'is_liked' => true,
                'likes_count' => 1,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $this->user->id,
            'likeable_type' => Article::class,
            'likeable_id' => $this->article->id,
        ]);
    }

    public function test_user_can_unlike_article()
    {
        // Сначала лайкаем
        $this->actingAs($this->user)
            ->postJson('/api/likes', [
                'likeable_type' => Article::class,
                'likeable_id' => $this->article->id,
            ]);

        // Потом убираем лайк
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/likes/" . Article::class . "/{$this->article->id}");

        $response->assertStatus(200)
            ->assertJson([
                'is_liked' => false,
                'likes_count' => 0,
            ]);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $this->user->id,
            'likeable_type' => Article::class,
            'likeable_id' => $this->article->id,
        ]);
    }

    public function test_user_can_toggle_like()
    {
        // Первый клик - лайк
        $response = $this->actingAs($this->user)
            ->postJson('/api/likes/toggle', [
                'likeable_type' => Article::class,
                'likeable_id' => $this->article->id,
            ]);

        $response->assertStatus(200)
            ->assertJson(['is_liked' => true]);

        // Второй клик - убираем лайк
        $response = $this->actingAs($this->user)
            ->postJson('/api/likes/toggle', [
                'likeable_type' => Article::class,
                'likeable_id' => $this->article->id,
            ]);

        $response->assertStatus(200)
            ->assertJson(['is_liked' => false]);
    }

    public function test_cannot_like_without_auth()
    {
        $response = $this->postJson('/api/likes', [
            'likeable_type' => Article::class,
            'likeable_id' => $this->article->id,
        ]);

        $response->assertStatus(401);
    }
}
