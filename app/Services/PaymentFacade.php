<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventJoined;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Illuminate\Support\Facades\Cache;

class PaymentFacade
{
    /**
     * Create Stripe PaymentIntent
     */
    public function createStripePayment(Event $event, int $studentId)
    {
        // 1. Capacity check
        $registeredCount = $event->eventJoined()
            ->where('status', 'registered')
            ->count();

        if ($registeredCount >= $event->max_capacity) {
            throw new \Exception('Event is full.');
        }

        // 2. Prevent duplicate registration
        $alreadyJoined = EventJoined::where('eventID', $event->eventID)
            ->where('studentID', $studentId)
            ->exists();

        if ($alreadyJoined) {
            throw new \Exception('You have already registered for this event.');
        }

        // 3. Handle FREE event (price = 0)
        if ($event->price <= 0) {
            EventJoined::create([
                'eventID'   => $event->eventID,
                'studentID' => $studentId,
                'status'    => 'registered',
            ]);

            return [
                'type'    => 'free',
                'message' => 'Successfully registered for free event.',
            ];
        }

        // 4. Stripe config check
        if (!config('services.stripe.secret')) {
            throw new \Exception('Stripe configuration missing.');
        }

        // 5. Create Stripe PaymentIntent for PAID event
        Stripe::setApiKey(config('services.stripe.secret'));

        return PaymentIntent::create([
            'amount'   => (int) round($event->price * 100), // MYR in cents
            'currency' => 'myr',
            'metadata' => [
                'event_id'   => $event->eventID,
                'student_id' => $studentId,
            ],
        ]);
    }

    public function sendVerificationCode(Event $event, int $studentId)
    {
        // Get user
        $user = User::findOrFail($studentId);
        
        // Log user details for debugging
        Log::info('Attempting to send verification code', [
            'user_id' => $user->id,
            'email_raw' => $user->email,
            'email_length' => strlen($user->email),
            'email_hex' => bin2hex($user->email),
            'event_id' => $event->eventID
        ]);
        
        // Generate 6-digit code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store in cache for 10 minutes
        $cacheKey = 'verification_code_' . $studentId . '_' . $event->eventID;
        Cache::put($cacheKey, [
            'code' => $verificationCode,
            'attempts' => 0,
            'verified' => false,
            'created_at' => now()
        ], now()->addMinutes(10));

        Log::info('Verification code generated and cached', [
            'cache_key' => $cacheKey,
        ]);

        try {
            // Ensure email is clean and valid - remove any URLs that might have been appended
            $email = trim($user->email);
            
            // Remove any URLs that might have been accidentally appended to the email
            // This handles cases where email might be "email@example.comhttp://..."
            $email = preg_replace('/https?:\/\/[^\s]+$/', '', $email);
            $email = trim($email);
            
            // Additional cleanup - remove any trailing URLs
            if (preg_match('/^(.+?@.+?\.[a-z]{2,})/i', $email, $matches)) {
                $email = $matches[1];
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::error('Invalid email format after cleanup', [
                    'original_email' => $user->email,
                    'cleaned_email' => $email,
                    'user_id' => $user->id
                ]);
                throw new \Exception('Invalid email address format. Please check your email in your profile.');
            }
            
            Log::info('Email cleaned and validated', [
                'original' => $user->email,
                'cleaned' => $email,
                'user_id' => $user->id
            ]);
            
            // Send simple text email with just the code
            Mail::raw(
                "Your Sportify Events verification code is: $verificationCode\n\n" .
                "This code will expire in 10 minutes.\n\n" .
                "Do not share this code with anyone.", 
                function ($message) use ($email, $event) {
                    $message->to($email)
                        ->subject('Payment Verification Code - ' . $event->event_name);
                }
            );
            
            Log::info('Verification email sent successfully', [
                'to' => $email,
                'event' => $event->event_name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send verification email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to send verification email: ' . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => 'Verification code sent to your email'
        ];
    }

    public function verifyCode(Event $event, int $studentId, string $code)
    {
        $cacheKey = 'verification_code_' . $studentId . '_' . $event->eventID;
        $verificationData = Cache::get($cacheKey);

        if (!$verificationData) {
            Log::warning('Verification code not found or expired', [
                'cache_key' => $cacheKey,
                'student_id' => $studentId,
                'event_id' => $event->eventID
            ]);
            throw new \Exception('Verification code expired or not found.');
        }

        // Check attempts
        if ($verificationData['attempts'] >= 3) {
            Cache::forget($cacheKey);
            Log::warning('Too many verification attempts', [
                'student_id' => $studentId,
                'event_id' => $event->eventID
            ]);
            throw new \Exception('Too many attempts. Please try again later.');
        }

        // Verify code
        if ($verificationData['code'] !== $code) {
            // Increment attempts
            $verificationData['attempts']++;
            Cache::put($cacheKey, $verificationData, now()->addMinutes(10));

            $attemptsLeft = 3 - $verificationData['attempts'];
            
            Log::warning('Invalid verification code entered', [
                'student_id' => $studentId,
                'event_id' => $event->eventID,
                'attempts_left' => $attemptsLeft
            ]);
            
            throw new \Exception('Invalid verification code. ' . $attemptsLeft . ' attempts left.');
        }

        // Mark as verified
        $verificationData['verified'] = true;
        Cache::put($cacheKey, $verificationData, now()->addMinutes(10));

        Log::info('Verification code verified successfully', [
            'student_id' => $studentId,
            'event_id' => $event->eventID
        ]);

        return [
            'success' => true,
            'message' => 'Code verified successfully'
        ];
    }

    public function isVerified(Event $event, int $studentId): bool
    {
        $cacheKey = 'verification_code_' . $studentId . '_' . $event->eventID;
        $verificationData = Cache::get($cacheKey);

        return $verificationData && $verificationData['verified'] === true;
    }

    /**
     * Clear verification data
     */
    public function clearVerification(Event $event, int $studentId): void
    {
        $cacheKey = 'verification_code_' . $studentId . '_' . $event->eventID;
        Cache::forget($cacheKey);
        
        Log::info('Verification data cleared', [
            'student_id' => $studentId,
            'event_id' => $event->eventID
        ]);
    }

    /**
     * Confirm Stripe payment, persist records, generate invoice, PDF, and send email
     */
    public function confirmStripePaymentAndInvoice(Event $event, int $studentId, string $paymentIntentId)
    {
        // Check if 2FA is verified first
        if (!$this->isVerified($event, $studentId)) {
            Log::warning('Payment attempt without 2FA verification', [
                'student_id' => $studentId,
                'event_id' => $event->eventID
            ]);
            throw new \Exception('Payment requires 2FA verification first.');
        }

        return DB::transaction(function () use ($event, $studentId, $paymentIntentId) {

            Log::info('Starting payment confirmation transaction', [
                'event_id' => $event->eventID,
                'student_id' => $studentId,
                'payment_intent_id' => $paymentIntentId
            ]);

            // 1️⃣ Create EventJoined record
            $eventJoined = EventJoined::create([
                'eventID'   => $event->eventID,
                'studentID' => $studentId,
                'status'    => 'registered',
                'joinedDate'=> now(),
            ]);

            Log::info('EventJoined record created', [
                'event_joined_id' => $eventJoined->eventJoinedID
            ]);

            // 2️⃣ Create Payment record
            $payment = Payment::create([
                'eventJoinedID'            => $eventJoined->eventJoinedID,
                'paymentMethod'            => 'stripe',
                'paymentAmount'            => $event->price,
                'paymentDate'              => now(),
                'stripe_payment_intent_id' => $paymentIntentId,
            ]);

            Log::info('Payment record created', [
                'payment_id' => $payment->paymentID,
                'amount' => $payment->paymentAmount
            ]);

            // 3️⃣ Create Invoice record
            $invoice = Invoice::create([
                'eventJoinedID'     => $eventJoined->eventJoinedID,
                'dateTimeGenerated' => now(),
            ]);

            Log::info('Invoice record created', [
                'invoice_id' => $invoice->invoiceID
            ]);

            // 4️⃣ Generate PDF
            $pdf = Pdf::loadView('invoices.invoice', [
                'invoice' => $invoice,
                'event'   => $event,
                'payment' => $payment
            ]);

            // 5️⃣ Get user's email
            $user = User::findOrFail($studentId);
            $email = trim($user->email);
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                Log::error('Invalid email format for payment confirmation', [
                    'email' => $email,
                    'user_id' => $user->id
                ]);
                throw new \Exception('Invalid email address format.');
            }

            Log::info('Sending payment confirmation email', [
                'to' => $email,
                'event' => $event->event_name
            ]);

            // 6️⃣ Send confirmation email with PDF attachment
            try {
                Mail::raw(
                    "Thank you for your payment!\n\n" .
                    "Your registration for {$event->event_name} has been confirmed.\n\n" .
                    "Payment Details:\n" .
                    "- Event: {$event->event_name}\n" .
                    "- Amount: RM " . number_format($event->price, 2) . "\n" .
                    "- Payment Method: Stripe\n" .
                    "- Payment Date: " . now()->format('F j, Y') . "\n" .
                    "- Payment ID: {$payment->paymentID}\n\n" .
                    "Your invoice is attached to this email.\n\n" .
                    "Thank you for using Sportify Events!",
                    function ($message) use ($email, $pdf, $event) {
                        $message->to($email)
                            ->subject('Payment Confirmation - ' . $event->event_name)
                            ->attachData($pdf->output(), 'invoice.pdf', [
                                'mime' => 'application/pdf',
                            ]);
                    }
                );

                Log::info('Payment confirmation email sent successfully', [
                    'to' => $email,
                    'payment_id' => $payment->paymentID
                ]);
                
            } catch (\Exception $e) {
                Log::error('Failed to send payment confirmation email', [
                    'error' => $e->getMessage(),
                    'to' => $email
                ]);
                // Don't throw exception - payment is already processed
            }

            // 7️⃣ Clear verification data after successful payment
            $this->clearVerification($event, $studentId);

            Log::info('Payment confirmation completed successfully', [
                'payment_id' => $payment->paymentID,
                'student_id' => $studentId,
                'event_id' => $event->eventID
            ]);

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
            // Committee sees all payments with user data
            return Payment::with([
                    'eventJoined.event', 
                    'eventJoined.invoice',
                    'eventJoined.user'  // ✅ Added user relationship
                ])
                ->orderBy('paymentDate', 'desc')
                ->get();
        } else {
            // Student sees only their payments (user is themselves)
            return Payment::with([
                    'eventJoined.event', 
                    'eventJoined.invoice',
                    'eventJoined.user'  // ✅ Added user relationship
                ])
                ->whereHas('eventJoined', function($query) use ($user) {
                    $query->where('studentID', $user->id);
                })
                ->orderBy('paymentDate', 'desc')
                ->get();
        }
    }
}