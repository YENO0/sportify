<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Event Payment | {{ $event->event_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Stripe.js library -->
    <script src="https://js.stripe.com/v3/"></script>
    
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Previous styles remain the same, add these new styles */

        .timer-container {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
            padding: 15px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 10px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(255, 107, 107, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 107, 107, 0); }
        }

        .timer-container.expiring {
            background: linear-gradient(135deg, #ef4444, #f87171);
            animation: pulse 1s infinite;
        }

        .timer-text {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .timer-display {
            font-size: 2rem;
            font-weight: 700;
            font-family: monospace;
            letter-spacing: 2px;
        }

        .timer-warning {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        /* Modal for timeout */
        .timeout-modal {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .modal-header-timeout {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .modal-header-timeout::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #ef4444, #dc2626 100%);
        }

        .timeout-modal .checkmark-circle {
            background: rgba(255, 255, 255, 0.2);
        }

        .timeout-modal .checkmark {
            content: "\f00d";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
        }

        /* Disabled state */
        .disabled-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            border-radius: 14px;
            flex-direction: column;
            padding: 40px;
            text-align: center;
        }

        .disabled-overlay h3 {
            color: #ef4444;
            margin-bottom: 15px;
        }

        .disabled-overlay p {
            color: #6b7280;
            margin-bottom: 20px;
        }

        .retry-btn {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: opacity 0.3s;
        }

        .retry-btn:hover {
            opacity: 0.9;
        }

        .payment-expired {
            pointer-events: none;
            position: relative;
        }

        * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f4f6f8;
            padding: 30px;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h2 {
            margin: 0;
            font-size: 2.2rem;
        }

        .header p {
            margin: 10px 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .content {
            display: flex;
            flex-wrap: wrap;
        }

        .left, .right {
            padding: 35px;
            min-width: 300px;
        }

        .left {
            flex: 0 0 35%;
            border-right: 1px solid #eee;
        }

        .right {
            flex: 1;
            min-width: 500px;
        }

        .price {
            background: #10b981;
            color: white;
            display: inline-block;
            padding: 12px 25px;
            border-radius: 30px;
            font-size: 1.8rem;
            margin: 15px 0;
            min-width: 160px;
            text-align: center;
        }

        .method {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: .3s;
            width: 100%;
        }

        .method:hover {
            border-color: #c7d2fe;
        }

        .method.active {
            border-color: #6366f1;
            background: #eef2ff;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        }

        .method i {
            font-size: 1.8rem;
            margin-right: 15px;
            color: #6366f1;
            width: 40px;
        }

        .method-content {
            flex: 1;
        }

        .method strong {
            font-size: 1.1rem;
            display: block;
            margin-bottom: 5px;
        }

        .method small {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            padding: 16px;
            font-size: 1.1rem;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 20px;
            transition: opacity 0.3s;
            font-weight: 600;
        }

        .btn:hover:not(:disabled) {
            opacity: 0.9;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .hidden {
            display: none;
        }

        .notice {
            color: #6b7280;
            margin-top: 20px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 10px;
            text-align: center;
            font-size: 1rem;
        }

        /* Stripe Card Element Styles */
        .StripeElement {
            box-sizing: border-box;
            height: 55px;
            padding: 16px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background-color: white;
            margin: 20px 0 25px 0;
            transition: border-color 0.3s, box-shadow 0.3s;
            width: 100%;
        }

        .StripeElement--focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .StripeElement--invalid {
            border-color: #ef4444;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }

        .card-label {
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
            color: #374151;
            font-size: 1.1rem;
        }

        .processing {
            text-align: center;
            padding: 20px;
            color: #6366f1;
            margin: 15px 0;
            background: #f0f3ff;
            border-radius: 10px;
            font-size: 1.1rem;
        }

        .processing i {
            margin-right: 10px;
        }

        .error-message {
            color: #ef4444;
            margin-top: 15px;
            padding: 15px;
            background: #fee2e2;
            border-radius: 10px;
            display: none;
            font-size: 1rem;
        }
        
        .success-message {
            color: #10b981;
            margin-top: 15px;
            padding: 15px;
            background: #d1fae5;
            border-radius: 10px;
            display: none;
            font-size: 1rem;
        }

        h3 {
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 25px;
            color: #1f2937;
            padding-bottom: 10px;
            border-bottom: 2px solid #f3f4f6;
        }

        .left p {
            font-size: 1.1rem;
            margin: 15px 0;
            color: #4b5563;
        }

        .left p strong {
            color: #1f2937;
            min-width: 100px;
            display: inline-block;
        }

        .secure-note {
            text-align: center;
            margin-top: 15px;
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .secure-note i {
            margin-right: 8px;
            color: #10b981;
        }

        /* Success Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 20px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            max-width: 700px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transform: translateY(30px);
            transition: transform 0.3s ease;
            max-height: 85vh;
            overflow-y: auto;
        }

        /* Hide scrollbar for modal */
        .modal-container::-webkit-scrollbar {
            width: 0;
            background: transparent;
        }

        .modal-container {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        .modal-overlay.active .modal-container {
            transform: translateY(0);
        }

        .modal-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #10b981, #34d399);
        }

        .checkmark-circle {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        .checkmark {
            color: white;
            font-size: 40px;
            animation: checkmark 0.5s ease-in-out;
        }

        @keyframes checkmark {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(1); opacity: 1; }
        }

        .modal-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .modal-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 80%;
            margin: 0 auto;
        }

        .modal-content {
            padding: 40px 30px;
        }

        .confirmation-message {
            text-align: center;
            color: #4b5563;
            margin-bottom: 30px;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .confirmation-message i {
            color: #10b981;
            margin-right: 8px;
        }

        .payment-details {
            background: #f9fafb;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
        }

        .payment-details h2 {
            font-size: 1.4rem;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-details h2 i {
            color: #10b981;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-label {
            color: #6b7280;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .detail-label i {
            width: 20px;
            color: #9ca3af;
        }

        .detail-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .amount-value {
            color: #10b981;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .payment-id {
            background: #f0f9ff;
            padding: 12px 20px;
            border-radius: 12px;
            margin-top: 15px;
            font-size: 0.9rem;
            color: #0369a1;
            border: 1px solid #bae6fd;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .payment-id i {
            font-size: 1rem;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .modal-btn {
            flex: 1;
            padding: 16px 24px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .modal-btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
        }

        .modal-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }

        .modal-btn-secondary {
            background: white;
            color: #4b5563;
            border: 2px solid #e5e7eb;
        }

        .modal-btn-secondary:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .modal-footer {
            text-align: center;
            padding: 20px;
            color: #9ca3af;
            font-size: 0.9rem;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .modal-footer a {
            color: #6366f1;
            text-decoration: none;
        }

        .close-modal {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: background 0.3s;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 1000px) {
            .container {
                max-width: 95%;
            }
            
            .content {
                flex-direction: column;
            }
            
            .left, .right {
                width: 100%;
                min-width: 100%;
            }
            
            .left {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
            
            .header {
                padding: 25px;
            }
            
            .header h2 {
                font-size: 1.8rem;
            }
            
            .modal-container {
                max-width: 95%;
            }
            
            .modal-buttons {
                flex-direction: column;
            }
            
            .modal-btn {
                width: 100%;
            }
            
            .detail-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .detail-value {
                align-self: flex-end;
            }
        }

        /* Celebration animation */
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: #f00;
            opacity: 0;
            z-index: 1001;
            pointer-events: none;
        }

        /* Verification Code Modal Styles */
        .verification-modal .modal-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        }

        .code-inputs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 30px 0;
        }

        .code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: white;
            transition: all 0.3s;
        }

        .code-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }

        .code-input.filled {
            border-color: #10b981;
            background: #f0f9ff;
        }

        .code-input.error {
            border-color: #ef4444;
            background: #fee2e2;
        }

        .verification-info {
            text-align: center;
            color: #6b7280;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .verification-error {
            color: #ef4444;
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            background: #fee2e2;
            border-radius: 8px;
            display: none;
        }

        .resend-code {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
        }

        .resend-link {
            color: #6366f1;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
        }

        .resend-link:hover {
            text-decoration: underline;
        }

        .resend-link.disabled {
            color: #9ca3af;
            cursor: not-allowed;
            text-decoration: none;
        }

        .countdown {
            color: #ef4444;
            font-weight: 600;
        }

        .processing-verification {
            text-align: center;
            color: #6366f1;
            margin: 20px 0;
            font-size: 16px;
        }

        .processing-verification i {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card" id="payment-card">
        <div class="header">
            <h2>{{ $event->event_name }}</h2>
            <p>{{ $event->event_description }}</p>
        </div>
        <br/>

        <!-- Timer Section -->
        <div class="timer-container" id="timer-container">
            <div class="timer-text">
                <i class="fas fa-clock"></i> Complete your payment in:
            </div>
            <div class="timer-display" id="timer-display">03:00</div>
            <div class="timer-warning">
                Your payment session will expire after 3 minutes
            </div>
        </div>

        <div class="content">
            <!-- EVENT INFO -->
            <div class="left">
                <h3>Event Details</h3>
                <div class="price">
                    RM {{ number_format($event->price, 2) }}
                </div>
                <p><strong>Status:</strong> {{ ucfirst($event->status) }}</p>
                <p><strong>Capacity:</strong> {{ $event->max_capacity }}</p>
                <p><strong>Payment Time Limit:</strong> 3 minutes</p>
            </div>

            <!-- PAYMENT -->
            <div class="right">
                <h3>Payment Method</h3>

                @if(session('error'))
                    <div class="error-message" style="display: block;">
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="success-message" style="display: block;">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="method active" data-method="stripe">
                    <i class="fab fa-cc-stripe"></i>
                    <div class="method-content">
                        <strong>Credit / Debit Card</strong>
                        <small>Pay securely with Stripe</small>
                    </div>
                </div>

                <!-- STRIPE PAYMENT FORM -->
                <div id="stripe-section">
                    
                    <div class="error-message" id="card-errors"></div>
                    
                    <form id="stripe-form" method="POST">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->eventID }}">
                        <input type="hidden" name="student_id" value="{{ auth()->id() ?? '1' }}">
                        <input type="hidden" name="payment_intent_id" id="payment_intent_id">
                        
                        <label class="card-label">Card Details</label>
                        <div id="card-element">
                            <!-- Stripe Card Element will be inserted here -->
                        </div>
                        
                        <button type="submit" id="pay-btn" class="btn">
                            <i class="fas fa-lock" style="margin-right: 10px;"></i>
                            Pay RM {{ number_format($event->price, 2) }}
                        </button>
                        
                        <div class="secure-note">
                            <i class="fas fa-shield-alt"></i>
                            Your payment is secure and encrypted
                        </div>
                    </form>
                </div>

                <!-- COMING SOON MESSAGE -->
                <div id="coming-soon" class="hidden notice">
                    <i class="fas fa-tools" style="margin-right: 10px;"></i>
                    This payment method is not available yet. Please use Stripe for now.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verification Code Modal -->
<div class="modal-overlay verification-modal" id="verificationModal">
    <div class="modal-container">
        <button class="close-modal" id="closeVerificationModal">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-header">
            <div class="checkmark-circle">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>Payment Verification</h1>
            <p>Enter the 6-digit verification code sent to your email</p>
        </div>

        <div class="modal-content">
            <div class="verification-info">
                <p>For security purposes, please enter the verification code sent to:</p>
                <p style="font-weight: 600; color: #1f2937; margin-top: 5px;">
                    {{ auth()->user()->email ?? 'your email' }}
                </p>
            </div>

            <div class="code-inputs" id="codeInputs">
                <input type="text" class="code-input" maxlength="1" data-index="0" autocomplete="off">
                <input type="text" class="code-input" maxlength="1" data-index="1" autocomplete="off">
                <input type="text" class="code-input" maxlength="1" data-index="2" autocomplete="off">
                <input type="text" class="code-input" maxlength="1" data-index="3" autocomplete="off">
                <input type="text" class="code-input" maxlength="1" data-index="4" autocomplete="off">
                <input type="text" class="code-input" maxlength="1" data-index="5" autocomplete="off">
            </div>

            <div class="verification-error" id="verificationError">
                <i class="fas fa-exclamation-circle"></i>
                <span id="verificationErrorText">Invalid verification code. Please try again.</span>
            </div>

            <div class="processing-verification hidden" id="processingVerification">
                <i class="fas fa-spinner fa-spin"></i> Verifying code...
            </div>

            <div class="resend-code">
                <p>Didn't receive the code? 
                    <a href="javascript:void(0)" id="resendCode" class="resend-link">Resend code</a>
                    <span id="countdown" class="countdown hidden">(60s)</span>
                </p>
            </div>

            <div class="modal-buttons">
                <button class="modal-btn modal-btn-primary" id="verifyCodeBtn" disabled>
                    <i class="fas fa-check"></i>
                    Verify & Continue Payment
                </button>
                <button class="modal-btn modal-btn-secondary" id="cancelVerificationBtn">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal (initially hidden) -->
<div class="modal-overlay" id="successModal">
    <div class="modal-container">
        <button class="close-modal" id="closeModal">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-header">
            <div class="checkmark-circle">
                <i class="fas fa-check checkmark"></i>
            </div>
            <h1>Payment Successful!</h1>
            <p>Thank you for your payment. Your registration has been confirmed.</p>
        </div>

        <div class="modal-content">
            <div class="confirmation-message">
                <i class="fas fa-envelope"></i>
                A confirmation email has been sent to your registered email address.
            </div>

            <div class="payment-details">
                <h2><i class="fas fa-receipt"></i> Payment Details</h2>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-calendar-check"></i>
                        <span>Event</span>
                    </div>
                    <div class="detail-value" id="modal-event-name">{{ $event->event_name }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Amount Paid</span>
                    </div>
                    <div class="detail-value amount-value" id="modal-amount">RM{{ number_format($event->price, 2) }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-credit-card"></i>
                        <span>Payment Method</span>
                    </div>
                    <div class="detail-value" id="modal-method">Stripe</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Payment Date</span>
                    </div>
                    <div class="detail-value" id="modal-date">{{ now()->format('F j, Y') }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-info-circle"></i>
                        <span>Status</span>
                    </div>
                    <div class="detail-value" style="color: #10b981;">
                        <i class="fas fa-check-circle"></i> Completed
                    </div>
                </div>

                <div class="payment-id" id="modal-payment-id">
                    <i class="fas fa-hashtag"></i>
                    <span>Payment ID: <span id="payment-id-text">Loading...</span></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Timeout Modal -->
<div class="modal-overlay timeout-modal" id="timeoutModal" style="color:red;">
    <div class="modal-container" style="color:red">
        <div class="modal-header-timeout">
            <div class="checkmark-circle">
                <i class="fas fa-times checkmark"></i>
            </div>
            <h1>Payment Session Expired</h1>
            <p>Your payment session has timed out. Please start over.</p>
        </div>

        <div class="modal-content">
            <div class="confirmation-message">
                <i class="fas fa-exclamation-triangle" style="color:red;"></i>
                For security reasons, payment sessions expire after 3 minutes of inactivity.
            </div>

            <div class="payment-details">
                <h2><i class="fas fa-clock" style="color:red;"></i> What happened?</h2>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-hourglass-end"></i>
                        <span>Time Limit</span>
                    </div>
                    <div class="detail-value">3 minutes</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-calendar-check"></i>
                        <span>Event</span>
                    </div>
                    <div class="detail-value">{{ $event->event_name }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Amount</span>
                    </div>
                    <div class="detail-value">RM {{ number_format($event->price, 2) }}</div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">
                        <i class="fas fa-info-circle"></i>
                        <span>Status</span>
                    </div>
                    <div class="detail-value" style="color: #ef4444;">
                        <i class="fas fa-times-circle"></i> Expired
                    </div>
                </div>
            </div>

            <div class="modal-buttons">
                <button class="modal-btn modal-btn-primary" id="retryPaymentBtn">
                    <i class="fas fa-redo"></i>
                    Start New Payment
                </button>
                <button class="modal-btn modal-btn-secondary" id="cancelPaymentBtn">
                    <i class="fas fa-times"></i>
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM Elements
        const methods = document.querySelectorAll('.method');
        const stripeSection = document.getElementById('stripe-section');
        const comingSoon = document.getElementById('coming-soon');
        const cardErrors = document.getElementById('card-errors');
        const payButton = document.getElementById('pay-btn');
        const stripeForm = document.getElementById('stripe-form');
        const successModal = document.getElementById('successModal');
        const closeModal = document.getElementById('closeModal');
        const timeoutModal = document.getElementById('timeoutModal');
        const timerContainer = document.getElementById('timer-container');
        const timerDisplay = document.getElementById('timer-display');
        const retryPaymentBtn = document.getElementById('retryPaymentBtn');
        const cancelPaymentBtn = document.getElementById('cancelPaymentBtn');
        const paymentCard = document.getElementById('payment-card');
        
        // Verification modal elements
        const verificationModal = document.getElementById('verificationModal');
        const closeVerificationModal = document.getElementById('closeVerificationModal');
        const codeInputs = document.querySelectorAll('.code-input');
        const verificationError = document.getElementById('verificationError');
        const verificationErrorText = document.getElementById('verificationErrorText');
        const processingVerification = document.getElementById('processingVerification');
        const resendCodeBtn = document.getElementById('resendCode');
        const countdownElement = document.getElementById('countdown');
        const verifyCodeBtn = document.getElementById('verifyCodeBtn');
        const cancelVerificationBtn = document.getElementById('cancelVerificationBtn');
        
        // Timer variables
        let timeLeft = 3 * 60; // 3 minutes in seconds
        let timerInterval;
        let paymentCompleted = false;
        let paymentIntentId = null;
        
        // Verification variables
        let verificationCode = '';
        let verificationAttempts = 0;
        let maxVerificationAttempts = 3;
        let resendTimer = null;
        let resendTimeLeft = 60;
        
        // Check if we have a client secret (passed from Laravel controller)
        const clientSecret = "{{ $clientSecret ?? '' }}";
        const eventId = "{{ $event->eventID }}";
        const studentId = "{{ auth()->id() ?? '1' }}";
        
        // Initialize Stripe
        const stripe = Stripe("{{ config('services.stripe.key') }}");
        const elements = stripe.elements();
        
        // Create and mount Card Element
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#374151',
                    fontFamily: '"Poppins", sans-serif',
                    '::placeholder': {
                        color: '#9ca3af',
                    },
                },
                invalid: {
                    color: '#ef4444',
                }
            }
        });
        
        // Mount the card element on page load
        if (document.getElementById('card-element')) {
            // Only mount if not already mounted
            if (!document.getElementById('card-element').children.length) {
                cardElement.mount('#card-element');
            }
        }
        
        // Start the countdown timer
        function startTimer() {
            clearInterval(timerInterval);
            timeLeft = 3 * 60;
            updateTimerDisplay();
            
            timerInterval = setInterval(() => {
                if (paymentCompleted) {
                    clearInterval(timerInterval);
                    return;
                }
                
                timeLeft--;
                updateTimerDisplay();
                
                // Update timer container style based on time left
                if (timeLeft <= 30) {
                    timerContainer.classList.add('expiring');
                } else {
                    timerContainer.classList.remove('expiring');
                }
                
                // Time's up
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    handleTimeout();
                }
            }, 1000);
        }
        
        // Update timer display
        function updateTimerDisplay() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        // Handle timeout
        function handleTimeout() {
            // Disable payment form
            paymentCard.classList.add('payment-expired');
            
            // Add disabled overlay
            const disabledOverlay = document.createElement('div');
            disabledOverlay.className = 'disabled-overlay';
            disabledOverlay.innerHTML = `
                <h3><i class="fas fa-clock"></i> Payment Session Expired</h3>
                <p>Your payment session has timed out. Please refresh the page to start a new payment.</p>
                <button class="retry-btn" onclick="location.reload()">
                    <i class="fas fa-redo"></i> Refresh Page
                </button>
            `;
            paymentCard.appendChild(disabledOverlay);
            
            // Show timeout modal
            timeoutModal.classList.add('active');
            
            // Cancel Stripe payment intent if exists
            if (paymentIntentId) {
                cancelPaymentIntent(paymentIntentId);
            }
        }
        
        // Cancel Stripe payment intent
        async function cancelPaymentIntent(paymentIntentId) {
            try {
                const response = await fetch('/api/cancel-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        payment_intent_id: paymentIntentId
                    })
                });
                
                const result = await response.json();
                console.log('Payment intent cancelled:', result);
            } catch (error) {
                console.error('Error cancelling payment intent:', error);
            }
        }
        
        // Reset timer on user interaction
        function resetTimer() {
            if (!paymentCompleted) {
                startTimer();
            }
        }
        
        // Handle card validation errors
        cardElement.addEventListener('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
                cardErrors.style.display = 'block';
            } else {
                cardErrors.style.display = 'none';
            }
            resetTimer();
        });
        
        // Update payment UI based on selected method - FIXED VERSION
        function updatePaymentUI(method) {
            if (method === 'stripe') {
                stripeSection.classList.remove('hidden');
                comingSoon.classList.add('hidden');
                
                // Check if card element is already mounted, if not mount it
                if (!document.getElementById('card-element').children.length) {
                    cardElement.mount('#card-element');
                }
            } else {
                stripeSection.classList.add('hidden');
                comingSoon.classList.remove('hidden');
                
                // Don't unmount the card element, just hide it
                // This preserves the element when switching back
            }
            resetTimer();
        }
        
        // Method selection click handlers
        methods.forEach(m => {
            m.addEventListener('click', () => {
                methods.forEach(x => x.classList.remove('active'));
                m.classList.add('active');
                updatePaymentUI(m.dataset.method);
                resetTimer();
            });
        });
        
        // Initialize Stripe UI
        updatePaymentUI('stripe');
        
        // Modal controls
        closeModal.addEventListener('click', () => {
            successModal.classList.remove('active');
        });
        
        retryPaymentBtn.addEventListener('click', () => {
            location.reload();
        });
        
        cancelPaymentBtn.addEventListener('click', () => {
            timeoutModal.classList.remove('active');
            window.location.href = '/events';
        });
        
        // Close modal when clicking outside
        successModal.addEventListener('click', (e) => {
            if (e.target === successModal) {
                successModal.classList.remove('active');
            }
        });
        
        timeoutModal.addEventListener('click', (e) => {
            if (e.target === timeoutModal) {
                timeoutModal.classList.remove('active');
            }
        });
        
        // Close verification modal
        closeVerificationModal.addEventListener('click', () => {
            verificationModal.classList.remove('active');
            resetVerificationForm();
            enablePaymentButton();
        });
        
        cancelVerificationBtn.addEventListener('click', () => {
            verificationModal.classList.remove('active');
            resetVerificationForm();
            enablePaymentButton();
        });
        
        verificationModal.addEventListener('click', (e) => {
            if (e.target === verificationModal) {
                verificationModal.classList.remove('active');
                resetVerificationForm();
                enablePaymentButton();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                if (successModal.classList.contains('active')) {
                    successModal.classList.remove('active');
                }
                if (timeoutModal.classList.contains('active')) {
                    timeoutModal.classList.remove('active');
                }
                if (verificationModal.classList.contains('active')) {
                    verificationModal.classList.remove('active');
                    resetVerificationForm();
                    enablePaymentButton();
                }
            }
        });
        
        // Create confetti effect
        function createConfetti() {
            const colors = ['#10b981', '#6366f1', '#8b5cf6', '#f59e0b', '#ef4444'];
            
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.top = '-10px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.opacity = '1';
                    confetti.style.borderRadius = '50%';
                    confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
                    document.body.appendChild(confetti);
                    
                    // Animation
                    const animation = confetti.animate([
                        { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                        { transform: `translateY(${window.innerHeight}px) rotate(${Math.random() * 720}deg)`, opacity: 0 }
                    ], {
                        duration: Math.random() * 3000 + 2000,
                        easing: 'cubic-bezier(0.215, 0.61, 0.355, 1)'
                    });
                    
                    // Remove after animation
                    animation.onfinish = () => confetti.remove();
                }, i * 100);
            }
        }
        
        // Start the timer when page loads
        startTimer();
        
        // Reset timer on form interaction
        stripeForm.addEventListener('click', resetTimer);
        stripeForm.addEventListener('keydown', resetTimer);
        
        // Verification code input handling
        codeInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                const index = parseInt(this.dataset.index);
                const value = this.value;
                
                // Only allow numbers
                if (!/^\d*$/.test(value)) {
                    this.value = '';
                    return;
                }
                
                // Update verification code
                verificationCode = getVerificationCode();
                
                // Update input styling
                updateCodeInputStyles();
                
                // Enable/disable verify button
                verifyCodeBtn.disabled = verificationCode.length !== 6;
                
                // Auto-focus next input
                if (value && index < 5) {
                    codeInputs[index + 1].focus();
                }
                
                // Clear error when user starts typing
                if (verificationError.style.display === 'block') {
                    verificationError.style.display = 'none';
                }
            });
            
            input.addEventListener('keydown', function(e) {
                const index = parseInt(this.dataset.index);
                
                // Handle backspace
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    codeInputs[index - 1].focus();
                }
                
                // Handle arrow keys
                if (e.key === 'ArrowLeft' && index > 0) {
                    codeInputs[index - 1].focus();
                }
                if (e.key === 'ArrowRight' && index < 5) {
                    codeInputs[index + 1].focus();
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                const numbers = pastedData.replace(/\D/g, '').split('').slice(0, 6);
                
                numbers.forEach((num, i) => {
                    if (codeInputs[i]) {
                        codeInputs[i].value = num;
                    }
                });
                
                // Update verification code
                verificationCode = getVerificationCode();
                updateCodeInputStyles();
                verifyCodeBtn.disabled = verificationCode.length !== 6;
                
                // Focus last input
                const lastIndex = Math.min(numbers.length - 1, 5);
                if (codeInputs[lastIndex]) {
                    codeInputs[lastIndex].focus();
                }
            });
        });
        
        // Get current verification code
        function getVerificationCode() {
            return Array.from(codeInputs).map(input => input.value).join('');
        }
        
        // Update code input styles
        function updateCodeInputStyles() {
            codeInputs.forEach(input => {
                if (input.value) {
                    input.classList.add('filled');
                    input.classList.remove('error');
                } else {
                    input.classList.remove('filled');
                    input.classList.remove('error');
                }
            });
        }
        
        // Reset verification form
        function resetVerificationForm() {
            codeInputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled', 'error');
            });
            verificationCode = '';
            verificationError.style.display = 'none';
            processingVerification.classList.add('hidden');
            verifyCodeBtn.disabled = true;
            verificationAttempts = 0;
        }
        
        // Start resend countdown
        function startResendCountdown() {
            resendCodeBtn.classList.add('disabled');
            resendCodeBtn.onclick = null;
            countdownElement.classList.remove('hidden');
            resendTimeLeft = 60;
            
            updateCountdown();
            
            resendTimer = setInterval(() => {
                resendTimeLeft--;
                updateCountdown();
                
                if (resendTimeLeft <= 0) {
                    clearInterval(resendTimer);
                    resendCodeBtn.classList.remove('disabled');
                    resendCodeBtn.onclick = sendVerificationCode;
                    countdownElement.classList.add('hidden');
                    countdownElement.textContent = '(60s)';
                }
            }, 1000);
        }
        
        // Update countdown display
        function updateCountdown() {
            countdownElement.textContent = `(${resendTimeLeft}s)`;
        }
        
        // Send verification code (simulated)
        function sendVerificationCode() {
            // In a real app, you would make an AJAX call to send the code
            console.log('Sending verification code to email...');
            
            // Simulate sending code
            // For demo purposes, we'll generate a random 6-digit code
            const generatedCode = Math.floor(100000 + Math.random() * 900000).toString();
            console.log('Generated verification code (for demo):', generatedCode);
            
            // Store the code for verification (in real app, this would be server-side)
            window.generatedVerificationCode = generatedCode;
            
            // Show success message (in real app, show "Code sent" message)
            alert(`Verification code sent to your email. For demo: ${generatedCode}`);
            
            // Start resend countdown
            startResendCountdown();
        }
        
        // Verify the code
        async function verifyCode() {
            const enteredCode = getVerificationCode();
            
            if (enteredCode.length !== 6) {
                showVerificationError('Please enter all 6 digits');
                return false;
            }
            
            // Show processing
            processingVerification.classList.remove('hidden');
            verifyCodeBtn.disabled = true;
            
            // Simulate verification delay
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Check verification attempts
            verificationAttempts++;
            if (verificationAttempts >= maxVerificationAttempts) {
                showVerificationError('Too many attempts. Please try again later.');
                processingVerification.classList.add('hidden');
                verifyCodeBtn.disabled = true;
                return false;
            }
            
            // In a real app, you would verify with the server
            // For demo, we'll check against the generated code
            const isValid = window.generatedVerificationCode === enteredCode;
            
            processingVerification.classList.add('hidden');
            
            if (isValid) {
                // Close verification modal
                verificationModal.classList.remove('active');
                
                // Proceed with payment processing
                processPayment();
                return true;
            } else {
                showVerificationError('Invalid verification code. Please try again.');
                
                // Add error styling to inputs
                codeInputs.forEach(input => {
                    input.classList.add('error');
                });
                
                // Clear inputs and refocus first
                resetCodeInputs();
                return false;
            }
        }
        
        // Show verification error
        function showVerificationError(message) {
            verificationErrorText.textContent = message;
            verificationError.style.display = 'block';
        }
        
        // Reset code inputs but keep form
        function resetCodeInputs() {
            codeInputs.forEach(input => {
                input.value = '';
                input.classList.remove('filled');
            });
            verificationCode = '';
            verifyCodeBtn.disabled = true;
            codeInputs[0].focus();
        }
        
        // Enable payment button
        function enablePaymentButton() {
            payButton.disabled = false;
            payButton.innerHTML = '<i class="fas fa-lock" style="margin-right: 10px;"></i> Pay RM {{ number_format($event->price, 2) }}';
        }
        
        // Process payment after verification
        async function processPayment() {
            // Check if timer has expired
            if (timeLeft <= 0) {
                cardErrors.textContent = 'Payment session has expired. Please refresh the page.';
                cardErrors.style.display = 'block';
                enablePaymentButton();
                return;
            }
            
            // Disable button and show processing
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i> Processing...';
            
            try {
                // Check if client secret is available
                if (!clientSecret) {
                    throw new Error('Payment initialization failed. Please refresh the page and try again.');
                }
                
                // Confirm the payment with Stripe
                const { paymentIntent, error } = await stripe.confirmCardPayment(
                    clientSecret,
                    {
                        payment_method: {
                            card: cardElement,
                            billing_details: {
                                name: 'Customer'
                            }
                        }
                    }
                );
                
                if (error) {
                    throw new Error(error.message);
                }
                
                paymentIntentId = paymentIntent.id;
                
                if (paymentIntent.status === 'succeeded') {
                    // Mark payment as completed
                    paymentCompleted = true;
                    
                    // Set the payment intent ID in the hidden field
                    document.getElementById('payment_intent_id').value = paymentIntent.id;
                    
                    // Send payment confirmation to server via AJAX
                    const formData = new FormData(stripeForm);
                    
                    try {
                        const response = await fetch('{{ route("payments.confirm") }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });
                        
                        const result = await response.json();
                        
                        if (response.ok && result.success) {
                            // Stop the timer
                            clearInterval(timerInterval);
                            
                            // Update modal with actual payment data
                            if (result.payment_id) {
                                document.getElementById('payment-id-text').textContent = result.payment_id;
                            }
                            
                            // Create confetti effect
                            createConfetti();
                            
                            // Show success modal
                            setTimeout(() => {
                                successModal.classList.add('active');
                                enablePaymentButton();
                            }, 1000);
                            
                        } else {
                            throw new Error(result.message || 'Payment confirmation failed');
                        }
                    } catch (error) {
                        throw new Error('Failed to confirm payment: ' + error.message);
                    }
                } else {
                    throw new Error('Payment was not successful. Status: ' + paymentIntent.status);
                }
            } catch (error) {
                // Show error message
                cardErrors.textContent = error.message;
                cardErrors.style.display = 'block';
                
                // Reset button
                enablePaymentButton();
            }
        }
        
        // FORM SUBMIT HANDLER - Shows verification modal
        // Replace the FORM SUBMIT HANDLER section in your view with this:

        // FORM SUBMIT HANDLER - Shows verification modal
        stripeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Reset errors
            cardErrors.style.display = 'none';
            
            // Check if timer has expired
            if (timeLeft <= 0) {
                cardErrors.textContent = 'Payment session has expired. Please refresh the page.';
                cardErrors.style.display = 'block';
                return;
            }
            
            // Disable payment button
            payButton.disabled = true;
            payButton.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i> Sending code...';
            
            try {
                // Send verification code via AJAX
                const response = await fetch('{{ route("payment.send-verification") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        student_id: studentId
                    })
                });
                
                const result = await response.json();
                
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Failed to send verification code');
                }
                
                // Show demo code in console (remove in production)
                if (result.demo_code) {
                    console.log('DEMO: Verification code sent:', result.demo_code);
                    alert('DEMO MODE: Your verification code is: ' + result.demo_code + '\n\nCheck your email for the code.');
                }
                
                // Show verification modal after code is sent
                setTimeout(() => {
                    verificationModal.classList.add('active');
                    resetVerificationForm();
                    codeInputs[0].focus();
                    enablePaymentButton();
                }, 500);
                
            } catch (error) {
                console.error('Error sending verification code:', error);
                cardErrors.textContent = 'Failed to send verification code: ' + error.message;
                cardErrors.style.display = 'block';
                enablePaymentButton();
            }
        });

        // Update the sendVerificationCode function to actually call the API
        async function sendVerificationCode() {
            try {
                // Show loading state
                resendCodeBtn.textContent = 'Sending...';
                resendCodeBtn.classList.add('disabled');
                
                const response = await fetch('{{ route("payment.send-verification") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        student_id: studentId
                    })
                });
                
                const result = await response.json();
                
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Failed to send verification code');
                }
                
                // Show demo code in console (remove in production)
                if (result.demo_code) {
                    console.log('DEMO: Verification code resent:', result.demo_code);
                    alert('DEMO MODE: Your new verification code is: ' + result.demo_code + '\n\nCheck your email for the code.');
                    window.generatedVerificationCode = result.demo_code;
                }
                
                // Reset resend button text
                resendCodeBtn.textContent = 'Resend code';
                
                // Start resend countdown
                startResendCountdown();
                
            } catch (error) {
                console.error('Error resending verification code:', error);
                showVerificationError('Failed to resend code: ' + error.message);
                resendCodeBtn.textContent = 'Resend code';
                resendCodeBtn.classList.remove('disabled');
            }
        }

        // Update the verifyCode function to call the API
        async function verifyCode() {
            const enteredCode = getVerificationCode();
            
            if (enteredCode.length !== 6) {
                showVerificationError('Please enter all 6 digits');
                return false;
            }
            
            // Show processing
            processingVerification.classList.remove('hidden');
            verifyCodeBtn.disabled = true;
            
            try {
                // Call verification API
                const response = await fetch('{{ route("payment.verify-code") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        student_id: studentId,
                        code: enteredCode
                    })
                });
                
                const result = await response.json();
                
                processingVerification.classList.add('hidden');
                
                if (!response.ok || !result.success) {
                    verificationAttempts++;
                    
                    if (verificationAttempts >= maxVerificationAttempts) {
                        showVerificationError('Too many attempts. Please try again later.');
                        verifyCodeBtn.disabled = true;
                        return false;
                    }
                    
                    showVerificationError(result.message || 'Invalid verification code. Please try again.');
                    
                    // Add error styling to inputs
                    codeInputs.forEach(input => {
                        input.classList.add('error');
                    });
                    
                    // Clear inputs and refocus first
                    resetCodeInputs();
                    return false;
                }
                
                // Verification successful
                console.log('Verification successful');
                
                // Close verification modal
                verificationModal.classList.remove('active');
                
                // Proceed with payment processing
                await processPayment();
                return true;
                
            } catch (error) {
                console.error('Error verifying code:', error);
                processingVerification.classList.add('hidden');
                showVerificationError('Verification failed: ' + error.message);
                resetCodeInputs();
                return false;
            }
        }
        
        // Verify code button handler
        verifyCodeBtn.addEventListener('click', verifyCode);
        
        // Resend code button handler
        resendCodeBtn.addEventListener('click', sendVerificationCode);
        
    });
</script>
</body>
</html>