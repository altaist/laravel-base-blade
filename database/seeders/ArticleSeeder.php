<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use App\Enums\ArticleStatus;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем статьи для существующих пользователей
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('Нет пользователей для создания статей. Сначала создайте пользователей.');
            return;
        }

        // Создаем по 3-5 статей для каждого пользователя
        foreach ($users as $user) {
            Article::factory()
                ->count(rand(3, 5))
                ->for($user)
                ->create();
        }

        // Создаем несколько опубликованных статей
        Article::factory()
            ->count(10)
            ->published()
            ->withSeo()
            ->create();

        // Создаем несколько статей готовых к публикации
        Article::factory()
            ->count(5)
            ->readyToPublish()
            ->withSeo()
            ->create();

        // Создаем несколько черновиков
        Article::factory()
            ->count(8)
            ->draft()
            ->create();

        // Создаем несколько статей с изображениями
        Article::factory()
            ->count(6)
            ->published()
            ->withImage()
            ->withSeo()
            ->create();

        $this->command->info('Создано ' . Article::count() . ' статей');
    }
}
