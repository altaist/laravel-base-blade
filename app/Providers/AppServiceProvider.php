<?php

namespace App\Providers;

use App\Notifications\Channels\TelegramChannel;
use App\Services\NotificationService;
use App\Services\Telegram\TelegramBotManager;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем TelegramBotManager как синглтон
        $this->app->singleton(TelegramBotManager::class);

        // Регистрируем TelegramService с TelegramBotManager
        $this->app->singleton(TelegramService::class, function ($app) {
            return new TelegramService($app->make(TelegramBotManager::class));
        });

        // Регистрируем TelegramBotService для каждого бота из конфига
        $this->app->singleton('telegram.main', function ($app) {
            $telegramService = $app->make(TelegramService::class);
            return new TelegramBotService($telegramService, 'main');
        });

        $this->app->singleton('telegram.admin', function ($app) {
            $telegramService = $app->make(TelegramService::class);
            return new TelegramBotService($telegramService, 'admin');
        });

        // Регистрируем NotificationService
        $this->app->singleton(NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Используем Bootstrap 5 пагинацию
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Регистрируем Telegram канал для уведомлений
        $this->app->extend('notification.channels', function ($channels) {
            $channels['telegram'] = $this->app->make(TelegramChannel::class);
            return $channels;
        });

        // Морфинг маппинг для моделей
        Relation::morphMap([
            'article' => \App\Models\Article::class,
        ]);
    }
}
