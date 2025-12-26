<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all eventJoined records that are registered (not waitlisted or cancelled)
        $eventJoinedRecords = DB::table('eventJoined')
            ->where('status', 'registered')
            ->get();

        $paymentMethods = ['credit_card', 'debit_card', 'bank_transfer', 'e-wallet', 'cash'];

        foreach ($eventJoinedRecords as $eventJoined) {
            // Check if payment already exists
            $existingPayment = DB::table('payments')
                ->where('eventJoinedID', $eventJoined->eventJoinedID)
                ->first();

            if (!$existingPayment) {
                // Get event price
                $event = DB::table('events')->where('eventID', $eventJoined->eventID)->first();
                $paymentAmount = $event ? $event->price : rand(10, 30);
                $paymentDate = Carbon::parse($eventJoined->joinedDate)->addHours(rand(1, 48));

                // Insert payment
                $paymentID = DB::table('payments')->insertGetId([
                    'paymentMethod' => $paymentMethods[array_rand($paymentMethods)],
                    'paymentDate' => $paymentDate,
                    'paymentAmount' => $paymentAmount,
                    'eventJoinedID' => $eventJoined->eventJoinedID,
                    'created_at' => $paymentDate,
                    'updated_at' => $paymentDate,
                ]);

                // Update eventJoined with paymentID
                DB::table('eventJoined')
                    ->where('eventJoinedID', $eventJoined->eventJoinedID)
                    ->update(['paymentID' => $paymentID]);
            }
        }
    }
}