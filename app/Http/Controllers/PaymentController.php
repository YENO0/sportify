<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Payment;
use App\Models\EventJoined;
use App\Models\User;
use App\Services\PaymentFacade;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected PaymentFacade $paymentFacade;

    /**
     * Inject Payment Facade
     */
    public function __construct(PaymentFacade $paymentFacade)
    {
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * Show payment page
     */
    public function show($eventId)
    {
        $event = Event::findOrFail($eventId);

        if ($event->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Event is not open for registration.');
        }

        // Temporary fallback student ID
        $studentId = Auth::id() ?? 1;

        try {
            $paymentIntent = $this->paymentFacade
                ->createStripePayment($event, $studentId);

            return view('payments.show', [
                'event'        => $event,
                'clientSecret' => $paymentIntent->client_secret,
                'student_id'   => $studentId
            ]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Stripe payment initialization (legacy support)
     */
    public function stripePay(Request $request)
    {
        $request->validate([
            'event_id'    => 'required|exists:events,eventID',
            'student_id'  => 'required|integer',
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
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Confirm Stripe payment (AJAX)
     */
    public function stripeConfirm(Request $request)
    {
        $request->validate([
            'event_id'          => 'required|exists:events,eventID',
            'student_id'        => 'required|integer',
            'payment_intent_id' => 'required|string'
        ]);

        $event = Event::findOrFail($request->event_id);

        try {
            $payment = $this->paymentFacade->confirmStripePaymentAndInvoice(
                $event,
                $request->student_id,
                $request->payment_intent_id
            );

            return response()->json([
                'success'         => true,
                'message'         => 'Payment successful!',
                'payment_id'      => $payment->paymentID,
                'payment_amount'  => $payment->paymentAmount,
                'payment_method'  => $payment->paymentMethod,
                'payment_date'    => $payment->paymentDate,
                'event_name'      => $event->event_name,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show all events joined by the logged-in user
     * WITH successful payments
     */
    public function myEvents()
    {
        $studentId = Auth::id() ?? 1;

        $eventJoined = EventJoined::with([
                'event',
                'payment',
                'invoice'
            ])
            ->where('studentID', $studentId)
            ->whereHas('payment') // ensures payment exists
            ->where('status', 'registered')
            ->orderBy('joinedDate', 'desc')
            ->get();

        return view('payments.my-events', compact('eventJoined'));
    }

    /**
     * Payment success page (legacy)
     */
    public function success($paymentId)
    {
        $payment = Payment::with('eventJoined.event')
            ->findOrFail($paymentId);

        return view('payments.success', compact('payment'));
    }

    public function transactionHistory()
    {
        // For now, grab a user from the users table (ID = 1 for testing)
        $user = \App\Models\User::find(1);

        // Get payments using the facade
        $transactions = $this->paymentFacade->getTransactionHistory($user);

        return view('payments.transaction-history', compact('transactions', 'user'));
    }
}
