<?php

namespace App\Console\Commands;

use App\Models\AuthLink;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredAutoAuthTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-auth:cleanup {--dry-run : Показать что будет удалено без фактического удаления}';

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
        
        // Находим истекшие токены автологина
        $expiredTokens = AuthLink::where('auto_auth', true)
            ->where('expires_at', '<=', now('UTC'))
            ->get();

        if ($expiredTokens->isEmpty()) {
            $this->info('Истекших токенов автологина не найдено.');
            return 0;
        }

        $count = $expiredTokens->count();
        
        if ($isDryRun) {
            $this->info("Найдено {$count} истекших токенов автологина:");
            foreach ($expiredTokens as $token) {
                $this->line("- ID: {$token->id}, User: {$token->user_id}, Expires: {$token->expires_at}");
            }
            $this->info('Запустите без --dry-run для фактического удаления.');
            return 0;
        }

        // Удаляем истекшие токены
        $deletedCount = AuthLink::where('auto_auth', true)
            ->where('expires_at', '<=', now('UTC'))
            ->delete();

        Log::channel('security')->info('Очистка истекших токенов автологина', [
            'deleted_count' => $deletedCount,
            'command' => 'auto-auth:cleanup'
        ]);

        $this->info("Удалено {$deletedCount} истекших токенов автологина.");
        
        return 0;
    }
}