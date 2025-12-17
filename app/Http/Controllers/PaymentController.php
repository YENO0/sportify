<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\EventJoined;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    /**
     * Show the payment page for a specific event
     */
    public function show($eventId)
    {
        $event = Event::findOrFail($eventId);

        if ($event->status != 'approved') {
            return redirect()->back()->with('error', 'Event is not open for registration.');
        }

        return view('payments.show', compact('event'));
    }

    /**
     * Create Stripe PaymentIntent and return client secret
     */
    public function stripePay(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,eventID',
            'student_id' => 'required|integer', // ideally Auth::id()
        ]);

        $event = Event::findOrFail($request->event_id);

        // Capacity check
        $registeredCount = $event->eventJoineds()->where('status', 'registered')->count();
        if ($registeredCount >= $event->max_capacity) {
            return redirect()->back()->with('error', 'Event is full.');
        }

        // Set Stripe secret key
        Stripe::setApiKey(config('services.stripe.secret'));

        // Create PaymentIntent
        $paymentIntent = PaymentIntent::create([
            'amount' => $event->price * 100, // Stripe uses cents
            'currency' => 'usd',
            'metadata' => [
                'student_id' => $request->student_id,
                'event_id' => $event->eventID
            ],
        ]);

        // Return view with client secret
        return view('payments.stripe', [
            'clientSecret' => $paymentIntent->client_secret,
            'event' => $event,
            'student_id' => $request->student_id
        ]);
    }

    /**
     * Handle Stripe confirmation webhook (or manual confirmation after client-side)
     * This will create EventJoined and Payment records
     */
    public function stripeConfirm(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,eventID',
            'student_id' => 'required|integer',
            'payment_intent_id' => 'required|string'
        ]);

        $event = Event::findOrFail($request->event_id);

        // Check capacity
        $registeredCount = $event->eventJoineds()->where('status', 'registered')->count();
        if ($registeredCount >= $event->max_capacity) {
            return redirect()->back()->with('error', 'Event is full.');
        }

        // DB transaction
        DB::beginTransaction();
        try {
            // Create EventJoined
            $eventJoined = EventJoined::create([
                'eventID' => $event->eventID,
                'studentID' => $request->student_id,
                'status' => 'registered',
                'joinedDate' => now(),
            ]);

            // Create Payment
            $payment = Payment::create([
                'eventJoinedID' => $eventJoined->eventJoinedID,
                'paymentMethod' => 'stripe',
                'paymentAmount' => $event->price,
                'paymentDate' => now(),
                'stripe_payment_intent_id' => $request->payment_intent_id
            ]);

            DB::commit();

            return redirect()->route('payments.success', $payment->paymentID);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    /**
     * Show payment success page
     */
    public function success($paymentId)
    {
        $payment = Payment::with('eventJoined.event')->findOrFail($paymentId);

        return view('payments.success', compact('payment'));
    }
}
