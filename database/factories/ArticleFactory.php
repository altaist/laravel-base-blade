<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use App\Models\File;
use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->sentence(3);
        $slug = Str::slug($name);

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'slug' => $slug,
            'description' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraphs(5, true),
            'seo_title' => $name . ' | ' . config('app.name'),
            'seo_description' => $this->faker->text(150),
            'seo_h1_title' => $name,
            'status' => $this->faker->randomElement(ArticleStatus::cases()),
            'img_file_id' => null,
        ];
    }

    /**
     * Статья в статусе черновика
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::DRAFT,
        ]);
    }

    /**
     * Статья готовая к публикации
     */
    public function readyToPublish(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::READY_TO_PUBLISH,
        ]);
    }

    /**
     * Опубликованная статья
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::PUBLISHED,
        ]);
    }

    /**
     * Статья с изображением
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'img_file_id' => File::factory(),
        ]);
    }

    /**
     * Статья с SEO данными
     */
    public function withSeo(): static
    {
        return $this->state(function (array $attributes) {
            $seoTitle = $attributes['name'] . ' - SEO заголовок';
            $seoDescription = 'SEO описание для статьи: ' . $attributes['name'];
            $seoH1 = 'H1 заголовок: ' . $attributes['name'];

            return [
                'seo_title' => $seoTitle,
                'seo_description' => $seoDescription,
                'seo_h1_title' => $seoH1,
            ];
        });
    }

    /**
     * Статья без SEO данных
     */
    public function withoutSeo(): static
    {
        return $this->state(fn (array $attributes) => [
            'seo_title' => null,
            'seo_description' => null,
            'seo_h1_title' => null,
        ]);
    }
}
