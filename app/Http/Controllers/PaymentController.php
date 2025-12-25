<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Payment;
use App\Models\EventJoined;
use App\Models\User;
use App\Services\PaymentFacade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

            Log::info('Payment page loaded', [
                'event_id' => $event->eventID,
                'student_id' => $studentId,
                'payment_intent_id' => $paymentIntent->id
            ]);

            return view('payments.show', [
                'event'        => $event,
                'clientSecret' => $paymentIntent->client_secret,
                'student_id'   => $studentId
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load payment page', [
                'error' => $e->getMessage(),
                'event_id' => $eventId,
                'student_id' => $studentId
            ]);
            
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Send 2FA verification code (AJAX endpoint)
     */
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,eventID',
            'student_id' => 'required|integer'
        ]);

        $event = Event::findOrFail($request->event_id);

        try {
            $result = $this->paymentFacade->sendVerificationCode($event, $request->student_id);
            
            Log::info('2FA verification code sent via API', [
                'event_id' => $event->eventID,
                'student_id' => $request->student_id,
                'email' => User::find($request->student_id)->email ?? 'unknown'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send verification code via API', [
                'error' => $e->getMessage(),
                'event_id' => $request->event_id,
                'student_id' => $request->student_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Verify 2FA code (AJAX endpoint)
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,eventID',
            'student_id' => 'required|integer',
            'code'       => 'required|string|size:6'
        ]);

        $event = Event::findOrFail($request->event_id);

        try {
            $result = $this->paymentFacade->verifyCode(
                $event, 
                $request->student_id, 
                $request->code
            );
            
            Log::info('2FA code verified successfully via API', [
                'event_id' => $event->eventID,
                'student_id' => $request->student_id
            ]);
            
            return response()->json([
                'success' => true,
                'message' => $result['message']
            ]);
            
        } catch (\Exception $e) {
            Log::warning('2FA verification failed via API', [
                'error' => $e->getMessage(),
                'event_id' => $request->event_id,
                'student_id' => $request->student_id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Check if 2FA is verified (AJAX endpoint)
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'event_id'   => 'required|exists:events,eventID',
            'student_id' => 'required|integer'
        ]);

        $event = Event::findOrFail($request->event_id);

        try {
            $isVerified = $this->paymentFacade->isVerified($event, $request->student_id);
            
            return response()->json([
                'success' => true,
                'verified' => $isVerified
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Confirm Stripe payment (AJAX) - UPDATED with 2FA check
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
            // Check if 2FA is verified first
            if (!$this->paymentFacade->isVerified($event, $request->student_id)) {
                Log::warning('Payment confirmation attempted without 2FA verification', [
                    'event_id' => $request->event_id,
                    'student_id' => $request->student_id
                ]);
                throw new \Exception('Payment requires 2FA verification first.');
            }

            $payment = $this->paymentFacade->confirmStripePaymentAndInvoice(
                $event,
                $request->student_id,
                $request->payment_intent_id
            );

            Log::info('Payment completed successfully via API', [
                'payment_id' => $payment->paymentID,
                'event_id' => $event->eventID,
                'student_id' => $request->student_id,
                'amount' => $payment->paymentAmount
            ]);

            return response()->json([
                'success'         => true,
                'message'         => 'Payment successful!',
                'payment_id'      => $payment->paymentID,
                'payment_amount'  => $payment->paymentAmount,
                'payment_method'  => $payment->paymentMethod,
                'payment_date'    => $payment->paymentDate->format('F j, Y'),
                'event_name'      => $event->event_name,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment confirmation failed via API', [
                'error' => $e->getMessage(),
                'event_id' => $request->event_id,
                'student_id' => $request->student_id,
                'payment_intent_id' => $request->payment_intent_id ?? 'not provided'
            ]);
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
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
     * My events page
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
            ->whereHas('payment')
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

    /**
     * Transaction history page
     */
    public function transactionHistory()
    {
        $user = Auth::user() ?? User::find(1);

        $transactions = $this->paymentFacade->getTransactionHistory($user);

        return view('payments.transaction-history', compact('transactions', 'user'));
    }
}