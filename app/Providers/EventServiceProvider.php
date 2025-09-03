<?php

namespace App\Providers;

use App\Events\TelegramMessageReceived;
use App\Listeners\ProcessTelegramMessage;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        TelegramMessageReceived::class => [
            ProcessTelegramMessage::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
