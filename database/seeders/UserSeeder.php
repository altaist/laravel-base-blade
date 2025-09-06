<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use App\Services\Referral\ReferralService;
use App\Enums\Referral\ReferralLinkType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $referralService = app(ReferralService::class);
        $password = Hash::make('12345678');

        // Create admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => $password,
            'role' => UserRole::ADMIN,
        ]);

        // Create manager user
        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => $password,
            'role' => UserRole::MANAGER,
        ]);

        // Create regular users
        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => $password,
            'role' => UserRole::USER,
        ]);

        // Создаем реферальные ссылки для всех пользователей кроме админа
        $this->createReferralLinks($referralService, [$manager, $user]);
    }

    /**
     * Создать реферальные ссылки для пользователей
     */
    private function createReferralLinks(ReferralService $referralService, array $users): void
    {
        foreach ($users as $user) {
            // Основная ссылка
            $referralService->createLinkForUser($user, [
                'name' => 'Основная ссылка',
                'type' => ReferralLinkType::CUSTOM,
            ]);

            // Дополнительные ссылки для демонстрации
            $referralService->createLinkForUser($user, [
                'name' => 'Instagram',
                'type' => ReferralLinkType::SOCIAL,
                'redirect_url' => 'https://instagram.com',
            ]);

            $referralService->createLinkForUser($user, [
                'name' => 'Telegram',
                'type' => ReferralLinkType::MESSENGER,
                'redirect_url' => 'https://t.me',
            ]);

            $referralService->createLinkForUser($user, [
                'name' => 'Офлайн приглашения',
                'type' => ReferralLinkType::OFFLINE,
            ]);
        }
    }
}
