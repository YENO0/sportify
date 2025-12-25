<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventJoined;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EventRegistrationDummySeeder extends Seeder
{
    public function run(): void
    {
        $eventIds = Event::orderBy('eventID')->limit(3)->pluck('eventID');

        if ($eventIds->isEmpty()) {
            $eventIds = Event::factory()->count(3)->create()->pluck('eventID');
        }

        foreach ($eventIds as $eventId) {

            // Get 10 random existing students
            $students = Student::inRandomOrder()->limit(10)->get();

            // If not enough students, create more
            if ($students->count() < 10) {
                $students = Student::factory()->count(10)->create();
            }

            foreach ($students as $student) {
                EventJoined::updateOrCreate(
                    [
                        'eventID' => $eventId,
                        'studentID' => $student->id,
                    ],
                    [
                        'status' => 'registered',
                        'joinedDate' => now(),
                        'paymentID' => null,
                    ]
                );
            }
        }
    }
}
