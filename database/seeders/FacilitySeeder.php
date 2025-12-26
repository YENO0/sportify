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
        $facilities = [
            [
                'name' => 'Main Sports Hall',
            'type' => 'Indoor',
            'status' => 'Active',
                'description' => 'Large multi-purpose indoor hall suitable for badminton, basketball, volleyball, and events. Capacity: 500 people.',
            ],
            [
            'name' => 'Tennis Court A',
            'type' => 'Outdoor',
            'status' => 'Active',
                'description' => 'Standard outdoor tennis court with lighting. Available for booking.',
            ],
            [
                'name' => 'Tennis Court B',
                'type' => 'Outdoor',
                'status' => 'Active',
                'description' => 'Standard outdoor tennis court with lighting. Available for booking.',
            ],
            [
            'name' => 'Swimming Pool',
                'type' => 'Indoor',
                'status' => 'Active',
                'description' => 'Olympic-sized swimming pool with 8 lanes. Includes diving board.',
            ],
            [
                'name' => 'Football Field',
                'type' => 'Outdoor',
                'status' => 'Active',
                'description' => 'Full-size football field with artificial turf and floodlights.',
            ],
            [
                'name' => 'Basketball Court',
                'type' => 'Outdoor',
                'status' => 'Active',
                'description' => 'Outdoor basketball court with standard dimensions.',
            ],
            [
                'name' => 'Table Tennis Room',
                'type' => 'Indoor',
                'status' => 'Active',
                'description' => 'Indoor room with 4 table tennis tables.',
            ],
            [
                'name' => 'Gymnasium',
            'type' => 'Indoor',
            'status' => 'Maintenance',
                'description' => 'Fully equipped gym with weights and cardio equipment. Currently under maintenance.',
            ],
        ];

        foreach ($facilities as $facility) {
            Facility::updateOrCreate(
                ['name' => $facility['name']],
                $facility
            );
        }
    }
}
