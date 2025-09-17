<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SessionList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:list {--user= : ID пользователя для фильтрации} {--limit=50 : Количество записей для показа}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Показать активные сессии пользователей';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user');
        $limit = (int) $this->option('limit');

        $this->info('Активные сессии пользователей:');
        $this->newLine();

        try {
            $query = DB::table('sessions')
                ->leftJoin('users', 'sessions.user_id', '=', 'users.id')
                ->select(
                    'sessions.id',
                    'sessions.user_id',
                    'users.name as user_name',
                    'users.email as user_email',
                    'sessions.ip_address',
                    'sessions.user_agent',
                    'sessions.last_activity'
                )
                ->orderBy('sessions.last_activity', 'desc');

            if ($userId) {
                $query->where('sessions.user_id', $userId);
            }

            $sessions = $query->limit($limit)->get();

            if ($sessions->isEmpty()) {
                $this->info('Активных сессий не найдено.');
                return 0;
            }

            // Заголовок таблицы
            $this->table(
                ['ID', 'User ID', 'Имя', 'Email', 'IP', 'Последняя активность'],
                $sessions->map(function ($session) {
                    return [
                        $session->id,
                        $session->user_id ?? 'Гость',
                        $session->user_name ?? 'Неизвестно',
                        $session->user_email ?? 'Неизвестно',
                        $session->ip_address ?? 'Неизвестно',
                        $this->formatLastActivity($session->last_activity)
                    ];
                })
            );

            $this->newLine();
            $this->info("Показано: " . $sessions->count() . " сессий");

            // Статистика
            $totalSessions = DB::table('sessions')->count();
            $guestSessions = DB::table('sessions')->whereNull('user_id')->count();
            $userSessions = $totalSessions - $guestSessions;

            $this->newLine();
            $this->info("Общая статистика:");
            $this->line("Всего сессий: {$totalSessions}");
            $this->line("Пользователей: {$userSessions}");
            $this->line("Гостей: {$guestSessions}");

            return 0;

        } catch (\Exception $e) {
            $this->error('Ошибка при получении сессий: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Форматировать время последней активности
     */
    private function formatLastActivity($timestamp)
    {
        if (!$timestamp) {
            return 'Неизвестно';
        }

        $lastActivity = Carbon::createFromTimestamp($timestamp);
        $now = Carbon::now();
        
        if ($lastActivity->diffInMinutes($now) < 1) {
            return 'Только что';
        } elseif ($lastActivity->diffInMinutes($now) < 60) {
            return $lastActivity->diffInMinutes($now) . ' мин назад';
        } elseif ($lastActivity->diffInHours($now) < 24) {
            return $lastActivity->diffInHours($now) . ' ч назад';
        } else {
            return $lastActivity->diffInDays($now) . ' дн назад';
        }
    }

    /**
     * Форматировать дату
     */
    private function formatDate($date)
    {
        if (!$date) {
            return 'Неизвестно';
        }

        return Carbon::parse($date)->format('d.m.Y H:i');
    }
}
