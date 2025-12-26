<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Facility;
use Carbon\Carbon;
use Faker\Factory as Faker;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Committees (fallback to first user)
        $committees = User::where('role', User::ROLE_COMMITTEE)->get();
        if ($committees->isEmpty()) {
            $committees = collect([User::first()])->filter();
        }

        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $facilities = Facility::all();

        $now = Carbon::now();
        $batchTag = $now->format('YmdHis');

        // 7 Upcoming (approved)
        for ($i = 1; $i <= 7; $i++) {
            $startDate = $now->copy()->addDays(rand(1, 60));
            $endDate = $startDate->copy()->addDays(rand(0, 2));

            $startHour = rand(8, 16);
            $startTime = sprintf('%02d:00', $startHour);
            $endTime = sprintf('%02d:00', min(20, $startHour + rand(1, 4)));

            $committee = $committees->random();
            $facilityId = $facilities->isNotEmpty() ? $facilities->random()->id : null;

            Event::create([
                'event_name' => "Seed Upcoming {$batchTag} #{$i}",
                'event_description' => $faker->paragraphs(2, true),
                'event_start_date' => $startDate->toDateString(),
                'event_end_date' => $endDate->toDateString(),
                'event_start_time' => $startTime,
                'event_end_time' => $endTime,
                'registration_due_date' => $startDate->copy()->subDays(rand(1, 7))->toDateString(),
                'max_capacity' => rand(30, 300),
                'price' => $faker->randomFloat(2, 0, 50),
                'facility_id' => $facilityId,
                'committee_id' => $committee?->id,

                'status' => 'approved',
                'event_status' => 'Upcoming',
                'registration_status' => 'Open',

                'approved_by' => $admin?->id,
                'approved_at' => $now->copy()->subDays(rand(1, 10)),
                'rejection_remark' => null,
            ]);
        }

        // 3 Past (still approved) -> use Completed so it’s consistent with EventStatusService
        for ($i = 1; $i <= 3; $i++) {
            $startDate = $now->copy()->subDays(rand(1, 30));
            $endDate = $startDate->copy()->addDays(rand(0, 2));

            $startHour = rand(8, 16);
            $startTime = sprintf('%02d:00', $startHour);
            $endTime = sprintf('%02d:00', min(20, $startHour + rand(1, 4)));

            $committee = $committees->random();
            $facilityId = $facilities->isNotEmpty() ? $facilities->random()->id : null;

            Event::create([
                'event_name' => "Seed Past {$batchTag} #{$i}",
                'event_description' => $faker->paragraphs(2, true),
                'event_start_date' => $startDate->toDateString(),
                'event_end_date' => $endDate->toDateString(),
                'event_start_time' => $startTime,
                'event_end_time' => $endTime,
                'registration_due_date' => $startDate->copy()->subDays(rand(1, 7))->toDateString(),
                'max_capacity' => rand(30, 300),
                'price' => $faker->randomFloat(2, 0, 50),
                'facility_id' => $facilityId,
                'committee_id' => $committee?->id,

                'status' => 'approved',
                'event_status' => 'Completed',
                'registration_status' => 'Closed',

                'approved_by' => $admin?->id,
                'approved_at' => $now->copy()->subDays(rand(10, 60)),
                'rejection_remark' => null,
            ]);
        }
    }
}