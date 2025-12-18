<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Payment | {{ $event->event_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
        }

        .payment-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .event-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .event-header h2 {
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .event-header .event-description {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .payment-content {
            display: flex;
            flex-wrap: wrap;
        }

        .event-details {
            flex: 1;
            min-width: 300px;
            padding: 30px;
            border-right: 1px solid #eee;
        }

        .payment-section {
            flex: 1;
            min-width: 300px;
            padding: 30px;
            background-color: #fafafa;
        }

        .price-tag {
            display: inline-block;
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .event-info {
            margin-top: 25px;
        }

        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.05rem;
        }

        .info-item i {
            width: 30px;
            color: #6366f1;
            font-size: 1.2rem;
        }

        .payment-title {
            font-size: 1.5rem;
            color: #4f46e5;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .payment-methods {
            margin-bottom: 25px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            border-color: #c7d2fe;
            background-color: #f8fafc;
        }

        .payment-method.active {
            border-color: #6366f1;
            background-color: #eef2ff;
        }

        .payment-method i {
            font-size: 1.8rem;
            margin-right: 15px;
            color: #6366f1;
        }

        .payment-method .method-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .payment-method .method-desc {
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 3px;
        }

        .stripe-logo {
            height: 24px;
            margin-left: auto;
        }

        .btn-pay {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            padding: 18px;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn-pay:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(99, 102, 241, 0.2);
        }

        .btn-pay:active {
            transform: translateY(-1px);
        }

        .btn-pay i {
            margin-right: 10px;
            font-size: 1.4rem;
        }

        .secure-payment {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 0.9rem;
        }

        .secure-payment i {
            color: #10b981;
            margin-right: 8px;
        }

        .error-message {
            background-color: #fee2e2;
            border-left: 4px solid #ef4444;
            color: #991b1b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
        }

        .error-message i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .footer-note {
            text-align: center;
            margin-top: 25px;
            color: #9ca3af;
            font-size: 0.9rem;
            padding: 0 20px 20px;
        }

        @media (max-width: 768px) {
            .payment-content {
                flex-direction: column;
            }
            
            .event-details {
                border-right: none;
                border-bottom: 1px solid #eee;
            }
            
            .event-header h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="event-header">
                <h2>{{ $event->event_name }}</h2>
                <p class="event-description">{{ $event->event_description }}</p>
            </div>
            
            <div class="payment-content">
                <div class="event-details">
                    <h3 class="payment-title">Event Details</h3>
                    
                    <div class="price-tag">
                        RM{{ number_format($event->price, 2) }}
                    </div>
                    
                    <div class="event-info">
                        <div class="info-item">
                            <i class="fas fa-calendar-alt"></i>
                            <div>
                                <strong>Date & Time:</strong> To be announced
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Location:</strong> Online / Venue to be confirmed
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-user-friends"></i>
                            <div>
                                <strong>Organizer:</strong> Event Management System
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <i class="fas fa-info-circle"></i>
                            <div>
                                <strong>Payment includes:</strong> Event access, materials, and certificate
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="payment-section">
                    <h3 class="payment-title">Payment Method</h3>
                    
                    @if(session('error'))
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    @endif
                    
                    <div class="payment-methods">
                        <div class="payment-method active">
                            <i class="fab fa-cc-stripe"></i>
                            <div>
                                <div class="method-name">Credit/Debit Card</div>
                                <div class="method-desc">Pay securely with Stripe</div>
                            </div>
                            <img src="https://stripe.com/img/v3/home/twitter.png" class="stripe-logo" alt="Stripe">
                        </div>
                        
                        <div class="payment-method">
                            <i class="fas fa-university"></i>
                            <div>
                                <div class="method-name">Online Banking</div>
                                <div class="method-desc">FPX supported banks (Coming soon)</div>
                            </div>
                        </div>
                        
                        <div class="payment-method">
                            <i class="fas fa-wallet"></i>
                            <div>
                                <div class="method-name">E-Wallet</div>
                                <div class="method-desc">GrabPay, Touch 'n Go (Coming soon)</div>
                            </div>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('payments.stripe') }}" id="payment-form">
                        @csrf
                        <input type="hidden" name="event_id" value="{{ $event->eventID }}">
                        <input type="hidden" name="student_id" value="1"> <!-- replace with Auth::id() later -->
                        
                        <button type="submit" class="btn-pay">
                            <i class="fas fa-lock"></i> Pay RM{{ number_format($event->price, 2) }} Now
                        </button>
                    </form>
                    
                    <div class="secure-payment">
                        <i class="fas fa-shield-alt"></i>
                        <span>Your payment is secured with 256-bit SSL encryption</span>
                    </div>
                </div>
            </div>
            
            <div class="footer-note">
                <p>You will receive a confirmation email with event details after successful payment.</p>
                <p>Need help? Contact support@eventsystem.com</p>
            </div>
        </div>
    </div>

    <script>
        // Add interactivity to payment method selection
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethods = document.querySelectorAll('.payment-method');
            
            paymentMethods.forEach(method => {
                method.addEventListener('click', function() {
                    // Remove active class from all methods
                    paymentMethods.forEach(m => m.classList.remove('active'));
                    
                    // Add active class to clicked method
                    this.classList.add('active');
                    
                    // In a real application, you would change the form action based on selected method
                    // For now, we only have Stripe
                    if (this.querySelector('.fa-cc-stripe')) {
                        document.getElementById('payment-form').action = "{{ route('payments.stripe') }}";
                    }
                });
            });
            
            // Form submission animation
            const form = document.getElementById('payment-form');
            const submitBtn = form.querySelector('.btn-pay');
            
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Payment...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>