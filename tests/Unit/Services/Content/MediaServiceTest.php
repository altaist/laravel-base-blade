<?php

namespace Tests\Unit\Services\Content;

use App\Models\Article;
use App\Models\File;
use App\Services\Content\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaServiceTest extends TestCase
{
    use RefreshDatabase;

    private MediaService $mediaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaService = new MediaService();
    }

    /** @test */
    public function it_gets_image_url_from_file()
    {
        $file = File::factory()->create([
            'is_public' => true,
            'key' => 'test-key'
        ]);

        $result = $this->mediaService->getImageUrl($file);

        $this->assertIsString($result);
    }

    /** @test */
    public function it_returns_null_for_null_file()
    {
        $result = $this->mediaService->getImageUrl(null);

        $this->assertNull($result);
    }

    /** @test */
    public function it_gets_image_alt_from_file()
    {
        $file = File::factory()->create([
            'original_name' => 'test-image.jpg'
        ]);

        $result = $this->mediaService->getImageAlt($file);

        $this->assertEquals('test-image.jpg', $result);
    }

    /** @test */
    public function it_gets_image_alt_from_model()
    {
        $file = File::factory()->create();
        $article = new Article(['name' => 'Test Article']);

        $result = $this->mediaService->getImageAlt($file, $article);

        $this->assertEquals('Test Article', $result);
    }

    /** @test */
    public function it_returns_default_alt_for_null_file()
    {
        $result = $this->mediaService->getImageAlt(null);

        $this->assertEquals('', $result);
    }

    /** @test */
    public function it_checks_if_file_is_image()
    {
        $imageFile = File::factory()->create(['extension' => 'jpg']);
        $textFile = File::factory()->create(['extension' => 'txt']);

        $this->assertTrue($this->mediaService->isImage($imageFile));
        $this->assertFalse($this->mediaService->isImage($textFile));
        $this->assertFalse($this->mediaService->isImage(null));
    }

    /** @test */
    public function it_validates_image_files()
    {
        $imageFile = File::factory()->create(['extension' => 'jpg']);
        $textFile = File::factory()->create(['extension' => 'txt']);

        $this->assertTrue($this->mediaService->validateFile($imageFile));
        $this->assertFalse($this->mediaService->validateFile($textFile));
    }

    /** @test */
    public function it_gets_image_data_for_html()
    {
        $file = File::factory()->create([
            'original_name' => 'test-image.jpg',
            'is_public' => true,
            'key' => 'test-key'
        ]);
        $article = new Article(['name' => 'Test Article']);

        $result = $this->mediaService->getImageData($file, $article);

        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('alt', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertIsString($result['url']);
        $this->assertEquals('Test Article', $result['alt']);
    }

    /** @test */
    public function it_gets_image_data_with_null_file()
    {
        $article = new Article(['name' => 'Test Article']);

        $result = $this->mediaService->getImageData(null, $article);

        $this->assertArrayHasKey('url', $result);
        $this->assertArrayHasKey('alt', $result);
        $this->assertArrayHasKey('title', $result);
        $this->assertNull($result['url']);
        $this->assertEquals('', $result['alt']);
    }



    /** @test */
    public function it_handles_unsupported_image_types()
    {
        $file = File::factory()->create(['extension' => 'bmp']);

        $this->assertFalse($this->mediaService->isImage($file));
        $this->assertFalse($this->mediaService->validateFile($file));
    }

    /** @test */
    public function it_supports_all_image_types()
    {
        $supportedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

        foreach ($supportedTypes as $type) {
            $file = File::factory()->create(['extension' => $type]);
            $this->assertTrue($this->mediaService->isImage($file), "Type {$type} should be supported");
        }
    }
}
