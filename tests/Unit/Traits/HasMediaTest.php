<?php

namespace Tests\Unit\Traits;

use App\Models\Article;
use App\Models\File;
use App\Services\Content\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HasMediaTest extends TestCase
{
    use RefreshDatabase;

    private Article $article;
    private File $file;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mock(MediaService::class);
        
        $this->file = File::factory()->create([
            'public_url' => 'https://example.com/image.jpg',
            'original_name' => 'test-image.jpg',
            'extension' => 'jpg'
        ]);
        
        $this->article = Article::factory()->create([
            'name' => 'Test Article',
            'img_file_id' => $this->file->id
        ]);
    }

    /** @test */
    public function it_has_img_file_relationship()
    {
        $imgFile = $this->article->imgFile;

        $this->assertInstanceOf(File::class, $imgFile);
        $this->assertEquals($this->file->id, $imgFile->id);
    }

    /** @test */
    public function it_gets_main_image()
    {
        $mainImage = $this->article->getMainImage();

        $this->assertInstanceOf(File::class, $mainImage);
        $this->assertEquals($this->file->id, $mainImage->id);
    }

    /** @test */
    public function it_gets_image_url()
    {
        $imageUrl = $this->article->getImageUrl();

        $this->assertEquals('https://example.com/image.jpg', $imageUrl);
    }

    /** @test */
    public function it_checks_if_has_image()
    {
        $this->assertTrue($this->article->hasImage());

        $articleWithoutImage = Article::factory()->create([
            'img_file_id' => null
        ]);

        $this->assertFalse($articleWithoutImage->hasImage());
    }

    /** @test */
    public function it_sets_main_image()
    {
        $newFile = File::factory()->create();
        
        $this->article->setMainImage($newFile->id);

        $this->assertEquals($newFile->id, $this->article->img_file_id);
    }

    /** @test */
    public function it_gets_img_alt_attribute()
    {
        $alt = $this->article->getImgAltAttribute();

        $this->assertEquals('Test Article', $alt);
    }

    /** @test */
    public function it_handles_null_image_file()
    {
        $articleWithoutImage = Article::factory()->create([
            'img_file_id' => null
        ]);

        $this->assertNull($articleWithoutImage->getMainImage());
        $this->assertNull($articleWithoutImage->getImageUrl());
        $this->assertEquals('', $articleWithoutImage->getImgAltAttribute());
    }

    /** @test */
    public function it_handles_non_existent_image_file()
    {
        $articleWithInvalidImage = Article::factory()->create([
            'img_file_id' => 99999 // Несуществующий ID
        ]);

        $this->assertNull($articleWithInvalidImage->getMainImage());
        $this->assertNull($articleWithInvalidImage->getImageUrl());
    }

    /** @test */
    public function it_sets_main_image_to_null()
    {
        $this->article->setMainImage(null);

        $this->assertNull($this->article->img_file_id);
        $this->assertFalse($this->article->hasImage());
    }

    /** @test */
    public function it_uses_file_original_name_for_alt_when_no_model_name()
    {
        $articleWithoutName = Article::factory()->create([
            'name' => '',
            'img_file_id' => $this->file->id
        ]);

        $alt = $articleWithoutName->getImgAltAttribute();

        $this->assertEquals('test-image.jpg', $alt);
    }

    /** @test */
    public function it_uses_default_alt_when_no_name_and_no_file()
    {
        $articleWithoutNameAndImage = Article::factory()->create([
            'name' => '',
            'img_file_id' => null
        ]);

        $alt = $articleWithoutNameAndImage->getImgAltAttribute();

        $this->assertEquals('', $alt);
    }
}
