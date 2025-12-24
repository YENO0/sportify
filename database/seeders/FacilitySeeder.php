<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Facility::create([
            'name' => 'Main Hall',
            'type' => 'Indoor',
            'status' => 'Active',
            'description' => 'A large indoor hall suitable for badminton, basketball, and events.',
            'image' => null, // You can upload an image manually later
        ]);

        Facility::create([
            'name' => 'Tennis Court A',
            'type' => 'Outdoor',
            'status' => 'Active',
            'description' => 'Standard outdoor tennis court.',
            'image' => null,
        ]);

        Facility::create([
            'name' => 'Swimming Pool',
            'type' => 'Indoor',
            'status' => 'Maintenance',
            'description' => 'Olympic sized swimming pool.',
            'image' => null,
        ]);
    }
}
