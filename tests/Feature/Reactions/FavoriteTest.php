<?php

namespace Tests\Feature\Reactions;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
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

    public function test_user_can_add_article_to_favorites()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/favorites', [
                'favoritable_type' => Article::class,
                'favoritable_id' => $this->article->id,
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'is_favorited' => true,
                'favorites_count' => 1,
            ]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->user->id,
            'favoritable_type' => Article::class,
            'favoritable_id' => $this->article->id,
        ]);
    }

    public function test_user_can_remove_article_from_favorites()
    {
        // Сначала добавляем в избранное
        $this->actingAs($this->user)
            ->postJson('/api/favorites', [
                'favoritable_type' => Article::class,
                'favoritable_id' => $this->article->id,
            ]);

        // Потом убираем из избранного
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/favorites/" . Article::class . "/{$this->article->id}");

        $response->assertStatus(200)
            ->assertJson([
                'is_favorited' => false,
                'favorites_count' => 0,
            ]);

        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->user->id,
            'favoritable_type' => Article::class,
            'favoritable_id' => $this->article->id,
        ]);
    }

    public function test_user_can_toggle_favorite()
    {
        // Первый клик - добавляем в избранное
        $response = $this->actingAs($this->user)
            ->postJson('/api/favorites/toggle', [
                'favoritable_type' => Article::class,
                'favoritable_id' => $this->article->id,
            ]);

        $response->assertStatus(200)
            ->assertJson(['is_favorited' => true]);

        // Второй клик - убираем из избранного
        $response = $this->actingAs($this->user)
            ->postJson('/api/favorites/toggle', [
                'favoritable_type' => Article::class,
                'favoritable_id' => $this->article->id,
            ]);

        $response->assertStatus(200)
            ->assertJson(['is_favorited' => false]);
    }

    public function test_cannot_favorite_without_auth()
    {
        $response = $this->postJson('/api/favorites', [
            'favoritable_type' => Article::class,
            'favoritable_id' => $this->article->id,
        ]);

        $response->assertStatus(401);
    }
}
