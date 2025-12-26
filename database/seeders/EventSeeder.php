<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Facility;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get committee users
        $committees = User::where('role', User::ROLE_COMMITTEE)->get();
        if ($committees->isEmpty()) {
            $committees = collect([User::first()]);
        }

        // Get admin for approval
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        // Get facilities
        $facilities = Facility::all();

        $events = [
            [
                'event_name' => 'Annual Sports Gala',
                'event_description' => 'A celebration of athletic achievements with multiple sports competitions',
                'event_start_date' => Carbon::now()->addDays(30),
                'event_end_date' => Carbon::now()->addDays(31),
                'event_start_time' => '09:00',
                'event_end_time' => '18:00',
                'registration_due_date' => Carbon::now()->addDays(25),
                'max_capacity' => 200,
                'price' => 25.00,
                'facility_id' => $facilities->first()?->id,
                'committee_id' => $committees->first()?->id,
                'status' => 'approved',
                'registration_status' => 'Open',
                'event_status' => 'Upcoming',
                'approved_by' => $admin?->id,
                'approved_at' => Carbon::now()->subDays(5),
            ],
            [
                'event_name' => 'Inter-Club Basketball Tournament',
                'event_description' => 'Competitive basketball tournament between different clubs',
                'event_start_date' => Carbon::now()->addDays(45),
                'event_end_date' => Carbon::now()->addDays(47),
                'event_start_time' => '10:00',
                'event_end_time' => '17:00',
                'registration_due_date' => Carbon::now()->addDays(40),
                'max_capacity' => 100,
                'price' => 15.00,
                'facility_id' => $facilities->skip(1)->first()?->id,
                'committee_id' => $committees->first()?->id,
                'status' => 'approved',
                'registration_status' => 'Open',
                'event_status' => 'Upcoming',
                'approved_by' => $admin?->id,
                'approved_at' => Carbon::now()->subDays(3),
            ],
            [
                'event_name' => 'Tennis Championship',
                'event_description' => 'Singles and doubles tennis championship',
                'event_start_date' => Carbon::now()->addDays(60),
                'event_end_date' => Carbon::now()->addDays(62),
                'event_start_time' => '08:00',
                'event_end_time' => '20:00',
                'registration_due_date' => Carbon::now()->addDays(55),
            'max_capacity' => 50,
                'price' => 20.00,
                'facility_id' => $facilities->skip(2)->first()?->id,
                'committee_id' => $committees->first()?->id,
                'status' => 'pending',
                'registration_status' => 'NotOpen',
                'event_status' => 'Upcoming',
            ],
            [
                'event_name' => 'Swimming Competition',
                'event_description' => 'Various swimming events and races',
            'event_start_date' => Carbon::now()->addDays(20),
            'event_end_date' => Carbon::now()->addDays(20),
                'event_start_time' => '14:00',
                'event_end_time' => '18:00',
                'registration_due_date' => Carbon::now()->addDays(15),
                'max_capacity' => 80,
                'price' => 10.00,
            'facility_id' => null,
                'committee_id' => $committees->first()?->id,
            'status' => 'approved',
                'registration_status' => 'Open',
                'event_status' => 'Upcoming',
                'approved_by' => $admin?->id,
                'approved_at' => Carbon::now()->subDays(2),
            ],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['event_name' => $event['event_name']],
                $event
            );
        }
    }
}
