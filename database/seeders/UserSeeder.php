<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => UserRole::ADMIN,
        ]);

        // Create manager user
        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'role' => UserRole::MANAGER,
        ]);

        // Create regular users
        User::factory(3)->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'role' => UserRole::USER,
        ]);
    }
}
