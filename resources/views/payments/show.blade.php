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
            max-height: 90vh;
            overflow-y: auto;
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
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="header">
            <h2>{{ $event->event_name }}</h2>
            <p>{{ $event->event_description }}</p>
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

                <div class="method" data-method="fpx">
                    <i class="fas fa-university"></i>
                    <div class="method-content">
                        <strong>Online Banking (FPX)</strong>
                        <small>Coming soon</small>
                    </div>
                </div>

                <div class="method" data-method="ewallet">
                    <i class="fas fa-wallet"></i>
                    <div class="method-content">
                        <strong>E-Wallet</strong>
                        <small>Coming soon</small>
                    </div>
                </div>

                <!-- STRIPE PAYMENT FORM -->
                <div id="stripe-section">
                    <div class="processing hidden" id="processing">
                        <i class="fas fa-spinner fa-spin"></i> Processing payment...
                    </div>
                    
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

            <!-- Action Buttons -->
            <div class="modal-buttons">
                <a href="/" class="modal-btn modal-btn-primary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="/events" class="modal-btn modal-btn-secondary">
                    <i class="fas fa-calendar"></i> View More Events
                </a>
            </div>

            <!-- Optional: Download Receipt -->
            <div class="download-receipt">
                <a href="#" id="download-receipt">
                    <i class="fas fa-download"></i> Download Receipt
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
            <p>Need help? <a href="/contact">Contact Support</a> • <a href="/terms">Terms & Conditions</a></p>
            <p>You will receive event details via email 24 hours before the event.</p>
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
        const processingElement = document.getElementById('processing');
        const payButton = document.getElementById('pay-btn');
        const stripeForm = document.getElementById('stripe-form');
        const successModal = document.getElementById('successModal');
        const closeModal = document.getElementById('closeModal');
        
        // Check if we have a client secret (passed from Laravel controller)
        const clientSecret = "{{ $clientSecret ?? '' }}";
        
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
        
        // Mount the card element
        if (document.getElementById('card-element')) {
            cardElement.mount('#card-element');
        }
        
        // Handle card validation errors
        cardElement.addEventListener('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
                cardErrors.style.display = 'block';
            } else {
                cardErrors.style.display = 'none';
            }
        });
        
        // Update payment UI based on selected method
        function updatePaymentUI(method) {
            if (method === 'stripe') {
                stripeSection.classList.remove('hidden');
                comingSoon.classList.add('hidden');
                
                // Re-mount card element if it was unmounted
                if (!cardElement._component) {
                    cardElement.mount('#card-element');
                }
            } else {
                stripeSection.classList.add('hidden');
                comingSoon.classList.remove('hidden');
                
                // Unmount card element to clean up
                if (cardElement._component) {
                    cardElement.unmount();
                }
            }
        }
        
        // Method selection click handlers
        methods.forEach(m => {
            m.addEventListener('click', () => {
                methods.forEach(x => x.classList.remove('active'));
                m.classList.add('active');
                updatePaymentUI(m.dataset.method);
            });
        });
        
        // Initialize Stripe UI
        updatePaymentUI('stripe');
        
        // Modal controls
        closeModal.addEventListener('click', () => {
            successModal.classList.remove('active');
        });
        
        // Close modal when clicking outside
        successModal.addEventListener('click', (e) => {
            if (e.target === successModal) {
                successModal.classList.remove('active');
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && successModal.classList.contains('active')) {
                successModal.classList.remove('active');
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
        
        // FORM SUBMIT HANDLER - Now shows modal instead of redirecting
        stripeForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Reset errors
            cardErrors.style.display = 'none';
            
            // Show processing
            processingElement.classList.remove('hidden');
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
                
                if (paymentIntent.status === 'succeeded') {
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
                            // Update modal with actual payment data
                            if (result.payment_id) {
                                document.getElementById('payment-id-text').textContent = result.payment_id;
                            }
                            
                            // Create confetti effect
                            createConfetti();
                            
                            // Show success modal
                            setTimeout(() => {
                                successModal.classList.add('active');
                                processingElement.classList.add('hidden');
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
                payButton.disabled = false;
                payButton.innerHTML = '<i class="fas fa-lock" style="margin-right: 10px;"></i> Pay RM {{ number_format($event->price, 2) }}';
                processingElement.classList.add('hidden');
            }
        });
    });
</script>
</body>
</html>