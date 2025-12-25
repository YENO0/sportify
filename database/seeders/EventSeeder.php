<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Event::create([
            'event_name' => 'Laravel Bootcamp',
            'event_description' => 'Beginner to intermediate Laravel training',
            'event_poster' => null,
            'event_start_date' => Carbon::now()->addDays(10),
            'event_end_date' => Carbon::now()->addDays(11),
            'registration_due_date' => Carbon::now()->addDays(9),
            'max_capacity' => 50,
            'facility_id' => null,
            'price' => 30.00,
            'committee_id' => 1,
            'approved_by' => null,
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'rejection_remark' => null,
        ]);

        Event::create([
            'event_name' => 'Blockchain Seminar',
            'event_description' => 'Introduction to blockchain and Web3',
            'event_poster' => null,
            'event_start_date' => Carbon::now()->addDays(20),
            'event_end_date' => Carbon::now()->addDays(20),
            'registration_due_date' => Carbon::now()->addDays(18),
            'max_capacity' => 100,
            'facility_id' => null,
            'price' => 15.00,
            'committee_id' => 2,
            'approved_by' => null,
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'rejection_remark' => null,
        ]);
    }
}
