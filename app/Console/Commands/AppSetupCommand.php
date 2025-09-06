<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class AppSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:setup 
                            {--force : Принудительно выполнить без подтверждения}
                            {--no-seed : Не запускать сидеры}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Полная настройка приложения: миграции, сидеры и базовая конфигурация';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Начинаем настройку приложения...');
        $this->newLine();

        // Проверяем подтверждение
        if (!$this->option('force')) {
            if (!$this->confirm('Это действие удалит все данные из базы данных. Продолжить?')) {
                $this->warn('Настройка отменена.');
                return Command::FAILURE;
            }
        }

        try {
            // 1. Очистка и создание миграций
            $this->info('📊 Выполняем миграции...');
            $this->runMigration();
            $this->newLine();

            // 2. Запуск сидеров (если не отключено)
            if (!$this->option('no-seed')) {
                $this->info('🌱 Запускаем сидеры...');
                $this->runSeeders();
                $this->newLine();
            }

            // 3. Создание символических ссылок
            $this->info('🔗 Создаем символические ссылки...');
            $this->createStorageLinks();
            $this->newLine();

            // 4. Очистка кэша
            $this->info('🧹 Очищаем кэш...');
            $this->clearCache();
            $this->newLine();

            // 5. Показываем информацию о созданных пользователях
            if (!$this->option('no-seed')) {
                $this->showUserInfo();
            }

            $this->newLine();
            $this->info('✅ Настройка приложения завершена успешно!');
            $this->info('🎉 Приложение готово к использованию!');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Ошибка при настройке приложения: ' . $e->getMessage());
            $this->error('Файл: ' . $e->getFile() . ':' . $e->getLine());
            return Command::FAILURE;
        }
    }

    /**
     * Выполнить миграции
     */
    private function runMigration(): void
    {
        $this->line('  • Удаляем существующие таблицы...');
        Artisan::call('migrate:fresh', [], $this->output);
        
        if (Artisan::output()) {
            $this->line('  ' . Artisan::output());
        }
        
        $this->info('  ✅ Миграции выполнены успешно');
    }

    /**
     * Запустить сидеры
     */
    private function runSeeders(): void
    {
        $this->line('  • Создаем пользователей и реферальные ссылки...');
        Artisan::call('db:seed', [], $this->output);
        
        if (Artisan::output()) {
            $this->line('  ' . Artisan::output());
        }
        
        $this->info('  ✅ Сидеры выполнены успешно');
    }

    /**
     * Создать символические ссылки для storage
     */
    private function createStorageLinks(): void
    {
        try {
            Artisan::call('storage:link', [], $this->output);
            $this->info('  ✅ Символические ссылки созданы');
        } catch (\Exception $e) {
            $this->warn('  ⚠️  Не удалось создать символические ссылки: ' . $e->getMessage());
        }
    }

    /**
     * Очистить кэш
     */
    private function clearCache(): void
    {
        $commands = [
            'config:clear' => 'Конфигурация',
            'route:clear' => 'Маршруты',
            'view:clear' => 'Представления',
            'cache:clear' => 'Кэш приложения',
        ];

        foreach ($commands as $command => $description) {
            try {
                Artisan::call($command, [], $this->output);
                $this->line("  • Очищен кэш: {$description}");
            } catch (\Exception $e) {
                $this->warn("  ⚠️  Не удалось очистить кэш {$description}: " . $e->getMessage());
            }
        }
        
        $this->info('  ✅ Кэш очищен');
    }

    /**
     * Показать информацию о созданных пользователях
     */
    private function showUserInfo(): void
    {
        try {
            $users = \App\Models\User::with('referralLinks')->get();
            
            $this->info('👥 Созданные пользователи:');
            $this->newLine();
            
            foreach ($users as $user) {
                $this->line("  👤 {$user->name} ({$user->role->value})");
                $this->line("     📧 Email: {$user->email}");
                $this->line("     🔑 Пароль: 12345678");
                $this->line("     🔗 Реферальных ссылок: {$user->referralLinks->count()}");
                
                if ($user->referralLinks->count() > 0) {
                    $this->line("     📋 Ссылки:");
                    foreach ($user->referralLinks as $link) {
                        $redirectInfo = $link->redirect_url ? " → {$link->redirect_url}" : "";
                        $this->line("       • {$link->name} ({$link->type}): /ref/{$link->code}{$redirectInfo}");
                    }
                }
                $this->newLine();
            }
            
        } catch (\Exception $e) {
            $this->warn('⚠️  Не удалось получить информацию о пользователях: ' . $e->getMessage());
        }
    }
}