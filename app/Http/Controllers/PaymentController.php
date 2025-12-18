<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Payment;
use App\Services\PaymentFacade;

class PaymentController extends Controller
{
    protected PaymentFacade $paymentFacade;

    /**
     * Inject the Payment Facade
     */
    public function __construct(PaymentFacade $paymentFacade)
    {
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * Show the payment page for a specific event
     */
    public function show($eventId)
    {
        $event = Event::findOrFail($eventId);

        if ($event->status !== 'approved') {
            return redirect()->back()->with('error', 'Event is not open for registration.');
        }

        return view('payments.show', compact('event'));
    }

    /**
     * Create Stripe PaymentIntent (Facade handles complexity)
     */
    public function stripePay(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,eventID',
            'student_id'=> 'required|integer', // ideally Auth::id()
        ]);

        $event = Event::findOrFail($request->event_id);

        try {
            $paymentIntent = $this->paymentFacade
                ->createStripePayment($event, $request->student_id);

            return view('payments.stripe', [
                'clientSecret' => $paymentIntent->client_secret,
                'event'        => $event,
                'student_id'   => $request->student_id
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirm Stripe payment and persist records
     */
    public function stripeConfirm(Request $request)
    {
        $request->validate([
            'event_id'            => 'required|exists:events,eventID',
            'student_id'          => 'required|integer',
            'payment_intent_id'   => 'required|string'
        ]);

        $event = Event::findOrFail($request->event_id);

        try {
            $payment = $this->paymentFacade->confirmStripePayment(
                $event,
                $request->student_id,
                $request->payment_intent_id
            );

            return redirect()->route('payments.success', $payment->paymentID);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show payment success page
     */
    public function success($paymentId)
    {
        $payment = Payment::with('eventJoined.event')
            ->findOrFail($paymentId);

        return view('payments.success', compact('payment'));
    }
}
