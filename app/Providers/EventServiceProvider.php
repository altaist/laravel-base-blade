<?php

namespace App\Providers;

use App\Events\TelegramMessageReceived;
use App\Listeners\ProcessTelegramMessage;
use App\Listeners\SendRegistrationNotifications;
use Illuminate\Auth\Events\Registered;
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
        Registered::class => [
            SendRegistrationNotifications::class,
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
