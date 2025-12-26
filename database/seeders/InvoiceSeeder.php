<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all eventJoined records that have payments
        $eventJoinedWithPayments = DB::table('eventJoined')
            ->whereNotNull('paymentID')
            ->get();

        foreach ($eventJoinedWithPayments as $eventJoined) {
            // Check if invoice already exists
            $existingInvoice = DB::table('invoices')
                ->where('eventJoinedID', $eventJoined->eventJoinedID)
                ->first();

            if (!$existingInvoice) {
                // Get payment date
                $payment = DB::table('payments')->where('paymentID', $eventJoined->paymentID)->first();
                $invoiceDate = $payment ? Carbon::parse($payment->paymentDate)->addMinutes(rand(5, 30)) : Carbon::now();

                // Insert invoice
                DB::table('invoices')->insert([
                    'eventJoinedID' => $eventJoined->eventJoinedID,
                    'dateTimeGenerated' => $invoiceDate,
                    'created_at' => $invoiceDate,
                    'updated_at' => $invoiceDate,
                ]);
            }
        }
    }
}