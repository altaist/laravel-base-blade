<?php

namespace Tests\Unit\Traits;

use App\Http\Controllers\Content\ArticleController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidatesContentTest extends TestCase
{
    use RefreshDatabase;

    private ArticleController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new ArticleController(
            app(\App\Services\Content\ArticleService::class),
            app(\App\Services\Content\SeoService::class),
            app(\App\Services\Content\ContentService::class),
            app(\App\Services\Content\RichContentService::class),
            app(\App\Services\Content\MediaService::class)
        );
    }

    /** @test */
    public function it_returns_article_validation_rules()
    {
        $rules = $this->controller->getArticleValidationRules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('description', $rules);
        $this->assertArrayHasKey('content', $rules);
        $this->assertArrayHasKey('rich_content', $rules);
        $this->assertArrayHasKey('seo_title', $rules);
        $this->assertArrayHasKey('seo_description', $rules);
        $this->assertArrayHasKey('seo_h1_title', $rules);
        $this->assertArrayHasKey('img_file_id', $rules);

        $this->assertStringContainsString('required', $rules['name']);
        $this->assertStringContainsString('required', $rules['content']);
        $this->assertStringContainsString('nullable', $rules['description']);
        $this->assertStringContainsString('nullable', $rules['rich_content']);
    }

    /** @test */
    public function it_returns_article_update_validation_rules()
    {
        $rules = $this->controller->getArticleUpdateValidationRules();

        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('description', $rules);
        $this->assertArrayHasKey('content', $rules);
        $this->assertArrayHasKey('rich_content', $rules);
        $this->assertArrayHasKey('seo_title', $rules);
        $this->assertArrayHasKey('seo_description', $rules);
        $this->assertArrayHasKey('seo_h1_title', $rules);
        $this->assertArrayHasKey('img_file_id', $rules);

        $this->assertStringContainsString('sometimes|required', $rules['name']);
        $this->assertStringContainsString('sometimes|required', $rules['content']);
        $this->assertStringContainsString('nullable', $rules['description']);
        $this->assertStringContainsString('nullable', $rules['rich_content']);
    }

    /** @test */
    public function it_has_correct_field_lengths()
    {
        $rules = $this->controller->getArticleValidationRules();

        $this->assertStringContainsString('max:255', $rules['name']);
        $this->assertStringContainsString('max:60', $rules['seo_title']);
        $this->assertStringContainsString('max:160', $rules['seo_description']);
        $this->assertStringContainsString('max:100', $rules['seo_h1_title']);
    }

    /** @test */
    public function it_has_correct_validation_types()
    {
        $rules = $this->controller->getArticleValidationRules();

        $this->assertStringContainsString('string', $rules['name']);
        $this->assertStringContainsString('string', $rules['description']);
        $this->assertStringContainsString('string', $rules['content']);
        $this->assertStringContainsString('array', $rules['rich_content']);
        $this->assertStringContainsString('string', $rules['seo_title']);
        $this->assertStringContainsString('string', $rules['seo_description']);
        $this->assertStringContainsString('string', $rules['seo_h1_title']);
        $this->assertStringContainsString('exists:files,id', $rules['img_file_id']);
    }

    /** @test */
    public function it_differentiates_between_create_and_update_rules()
    {
        $createRules = $this->controller->getArticleValidationRules();
        $updateRules = $this->controller->getArticleUpdateValidationRules();

        $this->assertStringContainsString('required', $createRules['name']);
        $this->assertStringContainsString('sometimes|required', $updateRules['name']);

        $this->assertStringContainsString('required', $createRules['content']);
        $this->assertStringContainsString('sometimes|required', $updateRules['content']);
    }

    /** @test */
    public function it_handles_all_required_fields()
    {
        $rules = $this->controller->getArticleValidationRules();

        $requiredFields = ['name', 'content'];
        foreach ($requiredFields as $field) {
            $this->assertStringContainsString('required', $rules[$field]);
        }
    }

    /** @test */
    public function it_handles_all_optional_fields()
    {
        $rules = $this->controller->getArticleValidationRules();

        $optionalFields = ['description', 'rich_content', 'seo_title', 'seo_description', 'seo_h1_title', 'img_file_id'];
        foreach ($optionalFields as $field) {
            $this->assertStringContainsString('nullable', $rules[$field]);
        }
    }
}
