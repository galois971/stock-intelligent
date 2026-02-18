<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CreateTestUsersSeeder extends Seeder
{
    /**
     * Seed the application's database with test users.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Gestionnaire user
        User::updateOrCreate(
            ['email' => 'gestionnaire@example.com'],
            [
                'name' => 'Gestionnaire User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Observateur user
        User::updateOrCreate(
            ['email' => 'observateur@example.com'],
            [
                'name' => 'Observateur User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Test users created successfully!');
    }
}
