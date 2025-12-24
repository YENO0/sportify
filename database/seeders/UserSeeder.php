<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Student user
        DB::table('users')->insert([
            'name' => 'John Student',
            'email' => 'cllee8088@gmail.com',
            'gender' => 'male',
            'birthday' => '2000-01-01',
            'contact' => '0123456789',
            'password' => Hash::make('password123'), // always hash passwords
            'role' => 'committee',
            'profile_picture' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Committee user
        DB::table('users')->insert([
            'name' => 'Jane Committee',
            'email' => 'committee@example.com',
            'gender' => 'female',
            'birthday' => '1995-05-05',
            'contact' => '0987654321',
            'password' => Hash::make('password123'),
            'role' => 'committee',
            'profile_picture' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
