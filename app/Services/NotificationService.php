<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use App\Notifications\Admin\AdminUserRegistrationNotification;
use App\Notifications\Manager\ManagerUserRegistrationNotification;
use App\Notifications\User\UserRegistrationNotification;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function __construct(
        private readonly TelegramService $telegramService
    ) {}

    /**
     * Отправить все уведомления после регистрации пользователя
     */
    public function sendRegistrationNotifications(User $user): void
    {
        try {
            // Отправляем уведомления о регистрации
            $this->sendUserRegistrationNotification($user);
            $this->sendAdminRegistrationNotification($user);
            $this->sendManagerRegistrationNotification($user);

            Log::info('All registration notifications sent successfully', [
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send registration notifications', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Отправить уведомление пользователю о регистрации
     */
    public function sendUserRegistrationNotification(User $user): void
    {
        try {
            if (!$user->telegram_id) {
                Log::info('User has no Telegram ID, skipping registration notification', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
                return;
            }

            $user->notify(new UserRegistrationNotification());

            Log::info('User registration notification sent', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'telegram_id' => $user->telegram_id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send user registration notification', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Отправить уведомление админам о новой регистрации
     */
    public function sendAdminRegistrationNotification(User $newUser): void
    {
        try {
            $admins = User::where('role', UserRole::ADMIN)
                ->whereNotNull('telegram_id')
                ->get();

            if ($admins->isEmpty()) {
                Log::info('No admins with Telegram ID found for registration notification');
                return;
            }

            foreach ($admins as $admin) {
                $admin->notify(new AdminUserRegistrationNotification($newUser));
            }

            Log::info('Admin registration notifications sent', [
                'new_user_id' => $newUser->id,
                'new_user_email' => $newUser->email,
                'admins_count' => $admins->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send admin registration notifications', [
                'new_user_id' => $newUser->id,
                'new_user_email' => $newUser->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Отправить уведомление менеджерам о новой регистрации
     */
    public function sendManagerRegistrationNotification(User $newUser): void
    {
        try {
            $managers = User::where('role', UserRole::MANAGER)
                ->whereNotNull('telegram_id')
                ->get();

            if ($managers->isEmpty()) {
                Log::info('No managers with Telegram ID found for registration notification');
                return;
            }

            foreach ($managers as $manager) {
                $manager->notify(new ManagerUserRegistrationNotification($newUser));
            }

            Log::info('Manager registration notifications sent', [
                'new_user_id' => $newUser->id,
                'new_user_email' => $newUser->email,
                'managers_count' => $managers->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send manager registration notifications', [
                'new_user_id' => $newUser->id,
                'new_user_email' => $newUser->email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Отправить сообщение всем админам
     */
    public function sendToAdmins(string $message): void
    {
        try {
            $admins = User::where('role', UserRole::ADMIN)
                ->whereNotNull('telegram_id')
                ->get();

            foreach ($admins as $admin) {
                $this->telegramService->sendMessageToUser(
                    $admin->telegram_id,
                    $message,
                    TelegramService::FORMAT_HTML
                );
            }

            Log::info('Message sent to admins', [
                'message' => $message,
                'admins_count' => $admins->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send message to admins', [
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Отправить сообщение всем менеджерам
     */
    public function sendToManagers(string $message): void
    {
        try {
            $managers = User::where('role', UserRole::MANAGER)
                ->whereNotNull('telegram_id')
                ->get();

            foreach ($managers as $manager) {
                $this->telegramService->sendMessageToUser(
                    $manager->telegram_id,
                    $message,
                    TelegramService::FORMAT_HTML
                );
            }

            Log::info('Message sent to managers', [
                'message' => $message,
                'managers_count' => $managers->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send message to managers', [
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Отправить сообщение пользователю
     */
    public function sendToUser(User $user, string $message): void
    {
        try {
            if (!$user->telegram_id) {
                Log::info('User has no Telegram ID, cannot send message', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
                return;
            }

            $this->telegramService->sendMessageToUser(
                $user->telegram_id,
                $message,
                TelegramService::FORMAT_HTML
            );

            Log::info('Message sent to user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send message to user', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
        }
    }
}