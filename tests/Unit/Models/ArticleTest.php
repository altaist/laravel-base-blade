<?php

namespace Tests\Unit\Models;

use App\Models\Article;
use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_img_url_when_file_exists()
    {
        $user = User::factory()->create();
        $file = File::factory()->create([
            'is_public' => true,
            'key' => 'test-key-123',
        ]);
        
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'img_file_id' => $file->id,
        ]);

        $this->assertNotNull($article->img_url);
        $this->assertStringContainsString('test-key-123', $article->img_url);
    }

    public function test_img_url_is_null_when_no_file()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'img_file_id' => null,
        ]);

        $this->assertNull($article->img_url);
    }

    public function test_can_get_img_alt_text()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Article',
            'seo_h1_title' => 'SEO H1 Title',
        ]);

        $this->assertEquals('SEO H1 Title', $article->img_alt);
    }

    public function test_img_alt_falls_back_to_name()
    {
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Article',
            'seo_h1_title' => null,
        ]);

        $this->assertEquals('Test Article', $article->img_alt);
    }
}
