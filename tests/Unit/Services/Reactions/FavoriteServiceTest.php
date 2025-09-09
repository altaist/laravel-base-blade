<?php

namespace Tests\Unit\Services\Reactions;

use App\Models\Article;
use App\Models\User;
use App\Services\Reactions\FavoriteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteServiceTest extends TestCase
{
    use RefreshDatabase;

    private FavoriteService $favoriteService;
    private User $user;
    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->favoriteService = new FavoriteService();
        $this->user = User::factory()->create();
        $this->article = Article::factory()->create();
    }

    public function test_can_add_article_to_favorites()
    {
        $favorite = $this->favoriteService->addToFavorites($this->user, $this->article);

        $this->assertNotNull($favorite);
        $this->assertEquals($this->user->id, $favorite->user_id);
        $this->assertEquals(Article::class, $favorite->favoritable_type);
        $this->assertEquals($this->article->id, $favorite->favoritable_id);
    }

    public function test_can_remove_article_from_favorites()
    {
        // Сначала добавляем в избранное
        $this->favoriteService->addToFavorites($this->user, $this->article);

        // Потом убираем из избранного
        $result = $this->favoriteService->removeFromFavorites($this->user, $this->article);

        $this->assertTrue($result);
        $this->assertFalse($this->favoriteService->isFavorited($this->user, $this->article));
    }

    public function test_can_toggle_favorite()
    {
        // Первый toggle - добавляем в избранное
        $result = $this->favoriteService->toggleFavorite($this->user, $this->article);
        $this->assertTrue($result);
        $this->assertTrue($this->favoriteService->isFavorited($this->user, $this->article));

        // Второй toggle - убираем из избранного
        $result = $this->favoriteService->toggleFavorite($this->user, $this->article);
        $this->assertFalse($result);
        $this->assertFalse($this->favoriteService->isFavorited($this->user, $this->article));
    }

    public function test_can_count_favorites()
    {
        // Изначально 0 в избранном
        $this->assertEquals(0, $this->favoriteService->getFavoritesCount($this->article));

        // Добавляем в избранное
        $this->favoriteService->addToFavorites($this->user, $this->article);
        $this->assertEquals(1, $this->favoriteService->getFavoritesCount($this->article));
    }
}
