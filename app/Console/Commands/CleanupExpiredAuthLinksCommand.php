<?php

namespace App\Console\Commands;

use App\Services\AuthLinkService;
use Illuminate\Console\Command;

class CleanupExpiredAuthLinksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth-links:cleanup {--dry-run : Показать количество ссылок для удаления без удаления}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очистить истекшие ссылки авторизации';

    /**
     * Execute the console command.
     */
    public function handle(AuthLinkService $authLinkService)
    {
        if ($this->option('dry-run')) {
            $count = \App\Models\AuthLink::expired()->count();
            $this->info("Найдено {$count} истекших ссылок авторизации");
            return;
        }

        $count = $authLinkService->cleanupExpiredLinks();
        
        $this->info("Удалено {$count} истекших ссылок авторизации");
        
        if ($count > 0) {
            $this->info('Очистка завершена успешно');
        } else {
            $this->info('Истекших ссылок не найдено');
        }
    }
}
