<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SessionCloseAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:close-all {--confirm : Подтвердить закрытие всех сессий}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Закрыть все активные сессии пользователей';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('confirm')) {
            $this->error('ВНИМАНИЕ: Эта команда закроет ВСЕ активные сессии пользователей!');
            $this->info('Для подтверждения используйте флаг --confirm');
            return 1;
        }

        $this->info('Начинаем закрытие всех активных сессий...');

        try {
            // Получаем количество активных сессий
            $activeSessionsCount = DB::table('sessions')->count();
            $this->info("Найдено активных сессий: {$activeSessionsCount}");

            if ($activeSessionsCount === 0) {
                $this->info('Активных сессий не найдено.');
                return 0;
            }

            // Удаляем все сессии
            $deletedCount = DB::table('sessions')->delete();
            
            $this->info("Удалено сессий: {$deletedCount}");

            // Очищаем токены автологина
            /*
            $this->info('Очищаем токены автологина...');
            $deletedTokens = DB::table('auth_links')
                ->where('auto_auth', true)
                ->delete();
            
            $this->info("Удалено токенов автологина: {$deletedTokens}");

            // Логируем операцию
            Log::channel('security')->warning('Все активные сессии закрыты администратором', [
                'deleted_sessions' => $deletedCount,
                'deleted_tokens' => $deletedTokens,
                'executed_by' => 'console_command'
            ]);
*/
            $this->info('✅ Все активные сессии успешно закрыты!');
            $this->warn('Все пользователи будут разлогинены при следующем запросе.');

            return 0;

        } catch (\Exception $e) {
            $this->error('Ошибка при закрытии сессий: ' . $e->getMessage());
            Log::error('Ошибка при закрытии всех сессий', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
