<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventJoined;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
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
        $registeredCount = $event->eventJoineds()
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
            'currency' => 'usd',
            'metadata' => [
                'event_id'   => $event->eventID,
                'student_id' => $studentId
            ],
        ]);
    }

    /**
     * Confirm Stripe payment and persist records
     */
    public function confirmStripePayment(Event $event, int $studentId, string $paymentIntentId)
    {
        return DB::transaction(function () use ($event, $studentId, $paymentIntentId) {

            $eventJoined = EventJoined::create([
                'eventID'   => $event->eventID,
                'studentID' => $studentId,
                'status'    => 'registered',
                'joinedDate'=> now(),
            ]);

            return Payment::create([
                'eventJoinedID'              => $eventJoined->eventJoinedID,
                'paymentMethod'              => 'stripe',
                'paymentAmount'              => $event->price,
                'paymentDate'                => now(),
                'stripe_payment_intent_id'   => $paymentIntentId,
            ]);
        });
    }
}
