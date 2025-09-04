<?php

namespace App\Providers;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Регистрируем TelegramBotService как синглтон для основного бота
        $this->app->singleton('telegram.bot', function ($app) {
            $telegramService = $app->make(TelegramService::class);
            return new TelegramBotService($telegramService, 'bot');
        });

        // Регистрируем TelegramBotService как синглтон для админского бота
        $this->app->singleton('telegram.admin_bot', function ($app) {
            $telegramService = $app->make(TelegramService::class);
            return new TelegramBotService($telegramService, 'admin_bot');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
