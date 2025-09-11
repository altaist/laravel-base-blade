<?php

namespace App\Listeners;

use App\Services\NotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendRegistrationNotifications implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        
        // Отправляем все уведомления о регистрации
        $this->notificationService->sendRegistrationNotifications($user);
    }
}
