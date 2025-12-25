<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SportType;

class SportTypeSeeder extends Seeder
{
    public function run(): void
    {
        $sportTypes = [
            [
                'name' => 'Basketball',
                'description' => 'Basketball equipment and gear',
                'is_active' => true,
            ],
            [
                'name' => 'Football',
                'description' => 'Football and soccer equipment',
                'is_active' => true,
            ],
            [
                'name' => 'Tennis',
                'description' => 'Tennis rackets, balls, and accessories',
                'is_active' => true,
            ],
            [
                'name' => 'Badminton',
                'description' => 'Badminton rackets, shuttlecocks, and nets',
                'is_active' => true,
            ],
            [
                'name' => 'Volleyball',
                'description' => 'Volleyballs, nets, and court equipment',
                'is_active' => true,
            ],
            [
                'name' => 'Swimming',
                'description' => 'Swimming gear and pool equipment',
                'is_active' => true,
            ],
            [
                'name' => 'Athletics',
                'description' => 'Track and field equipment',
                'is_active' => true,
            ],
            [
                'name' => 'Table Tennis',
                'description' => 'Ping pong paddles, balls, and tables',
                'is_active' => true,
            ],
        ];

        foreach ($sportTypes as $sportType) {
            SportType::updateOrCreate(
                ['name' => $sportType['name']],
                array_merge($sportType, [
                    'slug' => \Illuminate\Support\Str::slug($sportType['name'])
                ])
            );
        }
    }
}

