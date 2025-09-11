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

        // Создаем 5 специальных статей о квадроциклах с моковыми изображениями
        $this->createQuadBikeArticles($users->first());

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

    /**
     * Создать специальные статьи о квадроциклах с моковыми изображениями
     */
    private function createQuadBikeArticles(User $user): void
    {
        $articles = [
            [
                'name' => 'Лучшие квадроциклы 2024 года: обзор топ-моделей',
                'description' => 'Подробный обзор самых популярных и надежных квадроциклов этого года. Сравнение характеристик, цены и отзывы владельцев.',
                'content' => 'В 2024 году рынок квадроциклов предлагает множество интересных моделей. Мы проанализировали лучшие варианты для разных целей: от спортивных моделей до утилитарных машин для работы и отдыха.',
                'img_file_id' => null, // Будет использоваться моковое изображение
            ],
            [
                'name' => 'Как выбрать первый квадроцикл: руководство для новичков',
                'description' => 'Полное руководство по выбору первого квадроцикла. На что обратить внимание, какие характеристики важны и как не ошибиться с выбором.',
                'content' => 'Выбор первого квадроцикла может показаться сложной задачей. В этом руководстве мы расскажем о всех важных аспектах: от типа двигателя до системы безопасности.',
                'img_file_id' => null,
            ],
            [
                'name' => 'Обслуживание квадроцикла: что нужно знать каждому владельцу',
                'description' => 'Основы технического обслуживания квадроцикла. Регулярные процедуры, замена расходников и диагностика неисправностей.',
                'content' => 'Правильное обслуживание квадроцикла - залог его долгой и надежной работы. Узнайте о всех необходимых процедурах и их периодичности.',
                'img_file_id' => null,
            ],
            [
                'name' => 'Безопасность на квадроцикле: правила и рекомендации',
                'description' => 'Важные правила безопасности при езде на квадроцикле. Экипировка, техника вождения и поведение в экстремальных ситуациях.',
                'content' => 'Безопасность должна быть приоритетом номер один для каждого водителя квадроцикла. Изучите основные правила и рекомендации по безопасной езде.',
                'img_file_id' => null,
            ],
            [
                'name' => 'Квадроциклы для детей: безопасность и выбор',
                'description' => 'Все о детских квадроциклах: от выбора подходящей модели до обучения ребенка безопасной езде.',
                'content' => 'Детские квадроциклы - отличный способ привить ребенку любовь к активному отдыху. Узнайте, как выбрать безопасную модель и обучить ребенка.',
                'img_file_id' => null,
            ]
        ];

        $imageUrls = [
            'https://picsum.photos/800/600?random=1',
            'https://via.placeholder.com/800x600/007bff/ffffff?text=Quad+Bike+1',
            'https://loremflickr.com/800/600/quadbike,atv',
            'https://picsum.photos/800/600?random=2',
            'https://via.placeholder.com/800x600/28a745/ffffff?text=Quad+Bike+2'
        ];

        foreach ($articles as $index => $articleData) {
            Article::create([
                'user_id' => $user->id,
                'name' => $articleData['name'],
                'slug' => \Illuminate\Support\Str::slug($articleData['name']),
                'description' => $articleData['description'],
                'content' => $articleData['content'],
                'status' => ArticleStatus::PUBLISHED,
                'seo_title' => $articleData['name'],
                'seo_description' => $articleData['description'],
                'seo_h1_title' => $articleData['name'],
                'img_file_id' => $articleData['img_file_id'],
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('Создано 5 специальных статей о квадроциклах');
    }
}
