<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@tashrif.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin123.'),
                'is_admin' => true,
            ]
        );

        // Create test regular user
        User::firstOrCreate(
            ['email' => 'user@tashrif.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('user123'),
                'is_admin' => false,
            ]
        );
    }
}