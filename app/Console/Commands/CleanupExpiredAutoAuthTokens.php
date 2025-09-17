<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupExpiredAutoAuthTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-auth:cleanup {--dry-run : Показать что будет удалено без удаления}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистить истекшие токены автологина';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 Режим предварительного просмотра (dry-run)');
        }

        $this->info('Очистка истекших токенов автологина...');

        try {
            // Находим истекшие токены
            $expiredTokens = DB::table('auth_links')
                ->where('auto_auth', true)
                ->where('expires_at', '<', Carbon::now())
                ->get();

            $this->info("Найдено истекших токенов: " . $expiredTokens->count());

            if ($expiredTokens->isEmpty()) {
                $this->info('Истекших токенов не найдено.');
                return 0;
            }

            // Показываем информацию о токенах
            if ($isDryRun) {
                $this->newLine();
                $this->info('Токены, которые будут удалены:');
                $this->table(
                    ['ID', 'User ID', 'Токен', 'Истек', 'Создан'],
                    $expiredTokens->map(function ($token) {
                        return [
                            $token->id,
                            $token->user_id ?? 'Неизвестно',
                            substr($token->token, 0, 20) . '...',
                            Carbon::parse($token->expires_at)->format('d.m.Y H:i'),
                            Carbon::parse($token->created_at)->format('d.m.Y H:i')
                        ];
                    })
                );
                $this->newLine();
                $this->warn('Для удаления запустите команду без флага --dry-run');
                return 0;
            }

            // Удаляем истекшие токены
            $deletedCount = DB::table('auth_links')
                ->where('auto_auth', true)
                ->where('expires_at', '<', Carbon::now())
                ->delete();

            $this->info("✅ Удалено истекших токенов: {$deletedCount}");

            // Логируем операцию
            Log::info('Очистка истекших токенов автологина', [
                'deleted_tokens' => $deletedCount,
                'executed_by' => 'console_command'
            ]);

            // Показываем статистику оставшихся токенов
            $activeTokens = DB::table('auth_links')
                ->where('auto_auth', true)
                ->where('expires_at', '>=', Carbon::now())
                ->count();

            $this->info("Активных токенов осталось: {$activeTokens}");

            return 0;

        } catch (\Exception $e) {
            $this->error('Ошибка при очистке токенов: ' . $e->getMessage());
            Log::error('Ошибка при очистке истекших токенов автологина', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}