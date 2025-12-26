<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventJoined;
use App\Models\User;
use Carbon\Carbon;

class EventJoinedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get events and students
        $events = Event::all();
        
        // Get students (users without admin/committee roles)
        $students = User::where(function($query) {
            $query->where('role', '!=', 'admin')
                  ->where('role', '!=', 'committee');
        })->limit(50)->get();

        if ($students->isEmpty()) {
            $students = User::limit(20)->get();
        }

        $eventJoinedRecords = [];

        foreach ($events as $event) {
            // Create 8-12 participants per event
            $numParticipants = rand(8, 12);
            $selectedStudents = $students->shuffle()->take($numParticipants);

            foreach ($selectedStudents as $index => $student) {
                $joinedDate = Carbon::now()->subDays(rand(1, 20));
                $status = ['registered', 'registered', 'registered', 'cancelled', 'waitlisted'][rand(0, 4)];

                $eventJoinedRecords[] = [
                    'eventID' => $event->eventID,
                    'studentID' => $student->id,
                    'paymentID' => null, // Will be updated by PaymentSeeder
                    'status' => $status,
                    'joinedDate' => $joinedDate,
                    'created_at' => $joinedDate,
                    'updated_at' => $joinedDate,
                ];
            }
        }

        // Insert all event joined records
        foreach ($eventJoinedRecords as $record) {
            \App\Models\EventJoined::updateOrCreate(
                [
                    'eventID' => $record['eventID'],
                    'studentID' => $record['studentID'],
                ],
                $record
            );
        }
    }
}