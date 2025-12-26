<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_ADMIN,
            ]
        );

        // Committee user
        User::updateOrCreate(
            ['email' => 'committee@test.com'],
            [
                'name' => 'Committee User',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_COMMITTEE,
            ]
        );

        // Student user
        User::updateOrCreate(
            ['email' => 'student@test.com'],
            [
                'name' => 'Student User',
                'password' => Hash::make('password123'),
                'role' => User::ROLE_STUDENT,
            ]
        );
    }
}

