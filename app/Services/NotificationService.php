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
            $telegramId = $user->getTelegramIdForBot('main');
            if (!$telegramId) {
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
                'telegram_id' => $telegramId,
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
            $admins = $this->getUsersByRole(UserRole::ADMIN);

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
            $managers = $this->getUsersByRole(UserRole::MANAGER);

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
            $admins = $this->getUsersByRole(UserRole::ADMIN);

            $sentCount = 0;
            foreach ($admins as $admin) {
                $telegramId = $admin->getTelegramIdForBot('main');
                if ($telegramId) {
                    $this->telegramService->sendMessageToUser(
                        $telegramId,
                        $message,
                        TelegramService::FORMAT_HTML
                    );
                    $sentCount++;
                }
            }

            Log::info('Message sent to admins', [
                'message' => $message,
                'total_admins' => $admins->count(),
                'sent_count' => $sentCount,
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
            $managers = $this->getUsersByRole(UserRole::MANAGER);

            $sentCount = 0;
            foreach ($managers as $manager) {
                $telegramId = $manager->getTelegramIdForBot('main');
                if ($telegramId) {
                    $this->telegramService->sendMessageToUser(
                        $telegramId,
                        $message,
                        TelegramService::FORMAT_HTML
                    );
                    $sentCount++;
                }
            }

            Log::info('Message sent to managers', [
                'message' => $message,
                'total_managers' => $managers->count(),
                'sent_count' => $sentCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send message to managers', [
                'message' => $message,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Получить пользователей по роли с Telegram ID
     */
    private function getUsersByRole(UserRole $role): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('role', $role)
            ->whereHas('telegramBots', function($query) {
                $query->where('bot_name', 'main');
            })
            ->get();
    }

    /**
     * Отправить сообщение пользователю
     */
    public function sendToUser(User $user, string $message): void
    {
        try {
            $telegramId = $user->getTelegramIdForBot('main');
            if (!$telegramId) {
                Log::info('User has no Telegram ID, cannot send message', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
                return;
            }

            $this->telegramService->sendMessageToUser(
                $telegramId,
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