<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ]
        );

        // Create an admin user
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'role' => User::ROLE_ADMIN,
                'password' => bcrypt('password'),
            ]
        );
        
        // Seed modules (order matters - seed dependencies first)
        $this->call([
            \Database\Seeders\SportTypeSeeder::class,
            \Database\Seeders\BrandSeeder::class,
            \Database\Seeders\EquipmentSeeder::class,
            \Database\Seeders\EventSeeder::class,
            \Database\Seeders\EventRegistrationDummySeeder::class,
            \Database\Seeders\DummyUserSeeder::class,
            \Database\Seeders\FacilitySeeder::class,
        ]);
    }
}
