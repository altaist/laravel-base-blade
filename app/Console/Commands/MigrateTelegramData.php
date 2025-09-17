<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserTelegramBot;
use Illuminate\Console\Command;

class MigrateTelegramData extends Command
{
    protected $signature = 'telegram:migrate-data 
        {--dry-run : Показать что будет перенесено без выполнения}
        {--force : Принудительно перезаписать существующие записи}';
    
    protected $description = 'Перенос существующих telegram_id в новую структуру user_telegram_bots';

    public function handle()
    {
        $this->info('🚀 Начинаем миграцию данных Telegram...');
        
        $users = User::whereNotNull('telegram_id')->get();
        $this->info("Найдено пользователей с telegram_id: {$users->count()}");
        
        if ($users->isEmpty()) {
            $this->warn('Нет пользователей для миграции.');
            return Command::SUCCESS;
        }
        
        $migrated = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($users as $user) {
            try {
                // Проверяем, не существует ли уже запись
                $exists = UserTelegramBot::where('user_id', $user->id)
                    ->where('bot_name', 'main')
                    ->exists();
                
                if ($exists && !$this->option('force')) {
                    $this->line("⏭️  Пропуск пользователя {$user->id} (уже существует)");
                    $skipped++;
                    continue;
                }
                
                if ($this->option('dry-run')) {
                    $this->line("📋 Будет перенесен: User ID {$user->id}, Telegram ID {$user->telegram_id}");
                    $migrated++;
                    continue;
                }
                
                // Создаем или обновляем запись
                UserTelegramBot::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'bot_name' => 'main',
                    ],
                    [
                        'telegram_id' => $user->telegram_id,
                        'bot_data' => [
                            'migrated_from_old_system' => true,
                            'migration_date' => now()->toISOString(),
                            'original_telegram_username' => $user->telegram_username,
                        ],
                    ]
                );
                
                $this->line("✅ Перенесен: User ID {$user->id} → Telegram ID {$user->telegram_id}");
                $migrated++;
                
            } catch (\Exception $e) {
                $this->error("❌ Ошибка для пользователя {$user->id}: {$e->getMessage()}");
                $errors++;
            }
        }
        
        $this->newLine();
        $this->info("📊 Результаты миграции:");
        $this->table(
            ['Статус', 'Количество'],
            [
                ['Перенесено', $migrated],
                ['Пропущено', $skipped],
                ['Ошибки', $errors],
            ]
        );
        
        if ($this->option('dry-run')) {
            $this->warn('🔍 Режим предварительного просмотра - данные не изменены');
            $this->info('Для выполнения миграции запустите команду без --dry-run');
        } else {
            $this->info('✅ Миграция завершена!');
            
            // Показываем статистику
            $totalBots = UserTelegramBot::count();
            $mainBots = UserTelegramBot::where('bot_name', 'main')->count();
            
            $this->info("📈 Статистика после миграции:");
            $this->info("- Всего записей в user_telegram_bots: {$totalBots}");
            $this->info("- Записей для основного бота: {$mainBots}");
        }
        
        return Command::SUCCESS;
    }
}