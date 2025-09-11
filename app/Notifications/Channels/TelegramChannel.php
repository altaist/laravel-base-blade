<?php

namespace App\Notifications\Channels;

use App\Services\Telegram\TelegramService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TelegramChannel
{
    public function __construct(
        private readonly TelegramService $telegramService
    ) {}

    /**
     * Send the given notification.
     * 
     * @param mixed $notifiable
     * @param Notification $notification
     */
    public function send($notifiable, Notification $notification): void
    {
        try {
            if (!method_exists($notification, 'toTelegram')) {
                Log::warning('Notification does not have toTelegram method', [
                    'notification' => get_class($notification),
                    'notifiable_id' => $notifiable->id ?? null,
                ]);
                return;
            }

            $message = call_user_func([$notification, 'toTelegram'], $notifiable);
            
            if (!$message) {
                Log::warning('Telegram notification message is empty', [
                    'notification' => get_class($notification),
                    'notifiable_id' => $notifiable->id ?? null,
                ]);
                return;
            }

            $telegramId = $this->getTelegramId($notifiable);
            
            if (!$telegramId) {
                Log::info('Telegram ID not found for notifiable', [
                    'notification' => get_class($notification),
                    'notifiable_id' => $notifiable->id ?? null,
                ]);
                return;
            }

            $success = $this->telegramService->sendMessageToUser(
                $telegramId,
                $message['text'] ?? '',
                $message['parse_mode'] ?? TelegramService::FORMAT_NONE
            );

            if (!$success) {
                Log::error('Failed to send Telegram notification', [
                    'notification' => get_class($notification),
                    'notifiable_id' => $notifiable->id ?? null,
                    'telegram_id' => $telegramId,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Telegram notification failed', [
                'notification' => get_class($notification),
                'notifiable_id' => $notifiable->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get the Telegram ID for the notifiable entity.
     */
    private function getTelegramId($notifiable): ?string
    {
        if (method_exists($notifiable, 'routeNotificationForTelegram')) {
            return $notifiable->routeNotificationForTelegram();
        }

        return $notifiable->telegram_id ?? null;
    }
}
