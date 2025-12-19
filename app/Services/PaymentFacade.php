<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventJoined;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentFacade
{
    /**
     * Create Stripe PaymentIntent
     */
    public function createStripePayment(Event $event, int $studentId)
    {
        // Capacity check
        $registeredCount = $event->eventJoined()
            ->where('status', 'registered')
            ->count();

        if ($registeredCount >= $event->max_capacity) {
            throw new \Exception('Event is full.');
        }

        // Prevent duplicate registration
        $alreadyJoined = EventJoined::where('eventID', $event->eventID)
            ->where('studentID', $studentId)
            ->exists();

        if ($alreadyJoined) {
            throw new \Exception('You have already registered for this event.');
        }

        // Stripe config check
        if (!config('services.stripe.secret')) {
            throw new \Exception('Stripe configuration missing.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        return PaymentIntent::create([
            'amount' => (int) round($event->price * 100),
            'currency' => 'myr',
            'metadata' => [
                'event_id'   => $event->eventID,
                'student_id' => $studentId
            ],
        ]);
    }

    /**
     * Confirm Stripe payment, persist records, generate invoice, PDF, and send email
     */
    public function confirmStripePaymentAndInvoice(Event $event, int $studentId, string $paymentIntentId)
    {
        return DB::transaction(function () use ($event, $studentId, $paymentIntentId) {

            // 1️⃣ Create EventJoined record
            $eventJoined = EventJoined::create([
                'eventID'   => $event->eventID,
                'studentID' => $studentId,
                'status'    => 'registered',
                'joinedDate'=> now(),
            ]);

            // 2️⃣ Create Payment record
            $payment = Payment::create([
                'eventJoinedID'            => $eventJoined->eventJoinedID,
                'paymentMethod'            => 'stripe',
                'paymentAmount'            => $event->price,
                'paymentDate'              => now(),
                'stripe_payment_intent_id' => $paymentIntentId,
            ]);

            // 3️⃣ Create Invoice record
            $invoice = Invoice::create([
                'eventJoinedID'     => $eventJoined->eventJoinedID,
                'dateTimeGenerated' => now(),
            ]);

            // 4️⃣ Generate PDF
            $pdf = Pdf::loadView('invoices.invoice', [
                'invoice' => $invoice,
                'event'   => $event,
                'payment' => $payment
            ]);

            // 5️⃣ Get user's email from users table
            $user = User::findOrFail($studentId);
            $email = $user->email;

            // 6️⃣ Send email with attached PDF
            Mail::send([], [], function ($message) use ($email, $pdf) {
                $message->to($email)
                    ->subject('Your Event Payment Invoice')
                    ->attachData($pdf->output(), 'invoice.pdf')
                    ->text('Thank you for your payment. Your invoice is attached.');
            });

            return $payment;
        });
    }

    /**
     * Get the role of a user dynamically from the database
     */
    protected function getUserRole(int $studentId): string
    {
        $user = User::findOrFail($studentId);
        return $user->role;
    }

    /**
     * Get transaction history depending on user role
     */
    public function getTransactionHistory(User $user)
    {
        $role = $this->getUserRole($user->id);

        if ($role === 'committee') {
            // Committee sees all payments
            return Payment::with(['eventJoined.event', 'eventJoined.invoice'])
                ->orderBy('paymentDate', 'desc')
                ->get();
        } else {
            // Student sees only their payments
            return Payment::with(['eventJoined.event', 'eventJoined.invoice'])
                ->whereHas('eventJoined', function($query) use ($user) {
                    $query->where('studentID', $user->id);
                })
                ->orderBy('paymentDate', 'desc')
                ->get();
        }
    }
}
