<?php

namespace App\Console\Commands;

use App\Models\AuthLink;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ShowActiveAuthLinksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth-links:show {--expired : Show expired links instead} {--user-id= : Filter by specific user ID} {--author-id= : Filter by specific author ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Показать все активные ссылки авторизации';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = AuthLink::query();

        // Фильтр по статусу ссылок
        if ($this->option('expired')) {
            $query->expired();
            $this->info('📅 Показываю истекшие ссылки:');
        } else {
            $query->active();
            $this->info('✅ Показываю активные ссылки:');
        }

        // Фильтр по пользователю
        if ($userId = $this->option('user-id')) {
            $query->where('user_id', $userId);
            $this->info("👤 Фильтр по пользователю ID: {$userId}");
        }

        // Фильтр по автору
        if ($authorId = $this->option('author-id')) {
            $query->where('author_id', $authorId);
            $this->info("👨‍💻 Фильтр по автору ID: {$authorId}");
        }

        // Получаем ссылки
        $links = $query->with(['user', 'author'])->orderBy('created_at', 'desc')->get();

        if ($links->isEmpty()) {
            $this->warn('🔍 Ссылки не найдены');
            return;
        }

        $this->info("📊 Найдено ссылок: {$links->count()}\n");

        // Создаем таблицу
        $headers = [
            'ID', 'Токен', 'Тип', 'Пользователь', 'Автор', 'Создана', 'Истекает', 'IP', 'User Agent'
        ];

        $rows = [];
        foreach ($links as $link) {
            $rows[] = [
                $link->id,
                substr($link->token, 0, 8) . '...',
                $this->getLinkType($link),
                $this->getUserInfo($link->user),
                $this->getUserInfo($link->author),
                $link->created_at->format('d.m.Y H:i'),
                $this->getExpiryInfo($link),
                $link->ip_address ?: '-',
                $this->truncateUserAgent($link->user_agent),
            ];
        }

        $this->table($headers, $rows);

        // Дополнительная статистика
        $this->showStatistics($links);
    }

    /**
     * Определить тип ссылки
     */
    private function getLinkType(AuthLink $link): string
    {
        if ($link->isForRegistration()) {
            return '📝 Регистрация';
        }
        return '🔐 Авторизация';
    }

    /**
     * Получить информацию о пользователе
     */
    private function getUserInfo($user): string
    {
        if (!$user) {
            return '-';
        }
        return "ID:{$user->id} ({$user->name})";
    }

    /**
     * Получить информацию об истечении
     */
    private function getExpiryInfo(AuthLink $link): string
    {
        if ($link->isExpired()) {
            return "❌ Истекла " . $link->expires_at->diffForHumans();
        }
        
        $remaining = $link->expires_at->diffForHumans(['parts' => 2]);
        return "⏰ {$remaining}";
    }

    /**
     * Обрезать User Agent
     */
    private function truncateUserAgent(?string $userAgent): string
    {
        if (!$userAgent) {
            return '-';
        }
        return strlen($userAgent) > 30 ? substr($userAgent, 0, 30) . '...' : $userAgent;
    }

    /**
     * Показать статистику
     */
    private function showStatistics($links): void
    {
        $this->newLine();
        $this->info('📈 Статистика:');

        $totalLinks = $links->count();
        $authLinks = $links->where('user_id', '!=', null)->count();
        $registrationLinks = $links->where('user_id', null)->count();
        
        $this->line("🔐 Ссылки авторизации: {$authLinks}");
        $this->line("📝 Ссылки регистрации: {$registrationLinks}");
        $this->line("📊 Всего: {$totalLinks}");

        // Группировка по времени создания
        $today = $links->where('created_at', '>=', Carbon::today())->count();
        $this->line("📅 Создано сегодня: {$today}");

        // Группировка по IP
        $uniqueIPs = $links->whereNotNull('ip_address')->pluck('ip_address')->unique()->count();
        $this->line("🌐 Уникальных IP: {$uniqueIPs}");
    }
}
