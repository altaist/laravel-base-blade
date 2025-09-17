<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SessionCloseUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:close-user {user_id : ID пользователя} {--confirm : Подтвердить закрытие сессий}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Закрыть все активные сессии конкретного пользователя';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');

        if (!$this->option('confirm')) {
            $this->error("ВНИМАНИЕ: Эта команда закроет ВСЕ активные сессии пользователя ID: {$userId}!");
            $this->info('Для подтверждения используйте флаг --confirm');
            return 1;
        }

        try {
            // Проверяем существование пользователя
            $user = User::find($userId);
            if (!$user) {
                $this->error("Пользователь с ID {$userId} не найден.");
                return 1;
            }

            $this->info("Закрываем сессии пользователя: {$user->name} ({$user->email})");

            // Получаем количество активных сессий пользователя
            $activeSessionsCount = DB::table('sessions')
                ->where('user_id', $userId)
                ->count();

            $this->info("Найдено активных сессий: {$activeSessionsCount}");

            if ($activeSessionsCount === 0) {
                $this->info('У пользователя нет активных сессий.');
                return 0;
            }

            // Удаляем сессии пользователя
            $deletedCount = DB::table('sessions')
                ->where('user_id', $userId)
                ->delete();
            
            $this->info("Удалено сессий: {$deletedCount}");

            // Очищаем токены автологина пользователя
            $this->info('Очищаем токены автологина пользователя...');
            $deletedTokens = DB::table('auth_links')
                ->where('user_id', $userId)
                ->where('auto_auth', true)
                ->delete();
            
            $this->info("Удалено токенов автологина: {$deletedTokens}");

            // Логируем операцию
            Log::channel('security')->warning('Сессии пользователя закрыты администратором', [
                'user_id' => $userId,
                'user_email' => $user->email,
                'deleted_sessions' => $deletedCount,
                'deleted_tokens' => $deletedTokens,
                'executed_by' => 'console_command'
            ]);

            $this->info("✅ Все активные сессии пользователя {$user->name} успешно закрыты!");
            $this->warn('Пользователь будет разлогинен при следующем запросе.');

            return 0;

        } catch (\Exception $e) {
            $this->error('Ошибка при закрытии сессий: ' . $e->getMessage());
            Log::error('Ошибка при закрытии сессий пользователя', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}
