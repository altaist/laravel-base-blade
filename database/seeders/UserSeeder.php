<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $password = Hash::make('12345678');

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => $password,
            'role' => UserRole::ADMIN,
        ]);

        // Create manager user
        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'password' => $password,
            'role' => UserRole::MANAGER,
        ]);

        // Create regular users
        User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => $password,
            'role' => UserRole::USER,
        ]);
    }
}
