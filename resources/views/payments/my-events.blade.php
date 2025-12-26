@extends('layouts.app')

@section('title', 'My Events')
@section('page-title', '')

@section('nav-links')
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
        :root {
            --primary-color: #6366f1;
            --primary-light: #818cf8;
            --secondary-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --dark-color: #1f2937;
            --gray-color: #6b7280;
            --light-gray: #f3f4f6;
            --white: #ffffff;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }

        /* Keep navbar layout stable; style only this page */
        body {
            background: linear-gradient(135deg, #f4f6f8 0%, #e5e7eb 100%) !important;
            padding: 0 !important;
        }

        .my-events-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--dark-color);
        }

        /* Hide scrollbars */
        .my-events-page::-webkit-scrollbar { display: none; }
        .my-events-page { -ms-overflow-style: none; scrollbar-width: none; }
        .my-events-page .table-container::-webkit-scrollbar { display: none; }
        .my-events-page .table-container { -ms-overflow-style: none; scrollbar-width: none; }

        /* Header Styles */
        .my-events-page .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .my-events-page .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        }

        .my-events-page .page-header h1 {
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .my-events-page .page-header p {
            color: var(--gray-color);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Tabs (My Events / Transaction History) */
        .my-events-page .page-tabs {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .my-events-page .page-tab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.08);
            color: var(--dark-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            border: 1px solid rgba(99, 102, 241, 0.15);
            transition: transform 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
        }

        .my-events-page .page-tab:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            background: rgba(99, 102, 241, 0.12);
        }

        .my-events-page .page-tab.active {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: var(--white);
            border-color: transparent;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--white);
            border-radius: 16px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: var(--white);
        }

        .stat-icon.events { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
        .stat-icon.amount { background: linear-gradient(135deg, #10b981, #34d399); }
        .stat-icon.active { background: linear-gradient(135deg, #3b82f6, #60a5fa); }
        .stat-icon.upcoming { background: linear-gradient(135deg, #f59e0b, #fbbf24); }

        .stat-content h3 {
            font-size: 0.9rem;
            color: var(--gray-color);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
        }

        /* Table Styles */
        .table-container {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            margin-bottom: 40px;
            overflow-x: auto;
        }

        .my-events-page table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .my-events-page thead {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        }

        .my-events-page th {
            padding: 20px;
            text-align: left;
            color: var(--white);
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
        }

        .my-events-page th:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .my-events-page th i {
            margin-right: 10px;
        }

        .my-events-page tbody tr {
            border-bottom: 1px solid var(--light-gray);
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        .my-events-page tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.05);
            transform: translateX(5px);
        }

        .my-events-page tbody tr:last-child {
            border-bottom: none;
        }

        .my-events-page td {
            padding: 20px;
            color: var(--dark-color);
            vertical-align: top;
        }

        /* Event Info */
        .event-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .event-name {
            font-weight: 600;
            font-size: 1.1rem;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .event-name i {
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        .event-description {
            color: var(--gray-color);
            font-size: 0.9rem;
            line-height: 1.4;
            max-width: 300px;
        }

        /* Status Badges */
        .status {
            display: inline-flex;
            align-items: center;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .status i {
            margin-right: 6px;
        }

        .status.registered { 
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .status.completed { 
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
        }
        .status.cancelled { 
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .status.pending { 
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .status.upcoming { 
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        /* Amount Display */
        .amount {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .amount i {
            color: var(--secondary-color);
            font-size: 1rem;
        }

        /* Date Display */
        .date {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .date-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-color);
            font-size: 0.9rem;
        }

        .date-item i {
            color: var(--primary-color);
            width: 16px;
        }

        .date-label {
            font-size: 0.8rem;
            color: var(--gray-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Payment Method */
        .payment-method {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 5px;
            font-size: 0.9rem;
            color: var(--gray-color);
        }

        .payment-method i {
            color: var(--primary-color);
        }

        /* No Events State */
        .no-events {
            text-align: center;
            padding: 80px 20px;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 40px;
        }

        .no-events-icon {
            font-size: 5rem;
            color: var(--light-gray);
            margin-bottom: 25px;
        }

        .no-events h3 {
            color: var(--gray-color);
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .no-events p {
            color: var(--gray-color);
            max-width: 500px;
            margin: 0 auto 30px;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .primary-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: var(--white);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }

        /* Footer */
        .page-footer {
            text-align: center;
            padding: 25px;
            color: var(--gray-color);
            font-size: 0.9rem;
            border-top: 1px solid var(--light-gray);
            margin-top: 40px;
        }

        .page-footer a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .page-footer a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .my-events-page { padding: 18px 12px; }

            .my-events-page .page-header h1 {
                font-size: 2rem;
                flex-direction: column;
                gap: 10px;
            }

            .my-events-page .page-header p { font-size: 1rem; }
            .stats-container { grid-template-columns: repeat(2, 1fr); }
            th, td { padding: 15px 10px; }
            .table-container { border-radius: 12px; }
            .event-description { max-width: 200px; }
        }

        @media (max-width: 480px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .event-description {
                display: none;
            }
            
            .date {
                flex-direction: column;
                gap: 5px;
            }
        }

        /* Print Styles */
        @media print {
            .my-events-page { padding: 0; }
            .my-events-page .page-header, .stats-container, .page-footer { display: none; }
            .table-container { box-shadow: none; border: 1px solid #ddd; }
            table { min-width: auto; }
        }
</style>
@endpush

@section('content')
<div class="my-events-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-calendar-check"></i> My Registered Events</h1>
        <p>Manage and view all the events you've registered for</p>
        @if(auth()->user()?->isStudent())
            <div class="page-tabs">
                <a class="page-tab active" href="{{ route('payments.my-events') }}">
                    <i class="fas fa-calendar-check"></i>
                    My Events
                </a>
                <a class="page-tab" href="{{ route('payments.transaction-history') }}">
                    <i class="fas fa-history"></i>
                    Transaction History
                </a>
            </div>
        @endif
    </div>

    @if($eventJoined->isEmpty())
        <!-- No Events State -->
        <div class="no-events">
            <div class="no-events-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h3>No Registered Events Yet</h3>
            <p>You haven't registered for any events. Explore our events and join exciting activities!</p>
            <a href="{{ route('events.approved') }}" class="primary-btn">
                <i class="fas fa-calendar-plus"></i> Browse All Events
            </a>
        </div>
    @else
        <!-- Stats Cards -->
        @php
            $totalAmount = $eventJoined->sum(function($joined) {
                return $joined->payment->paymentAmount ?? 0;
            });
            $activeEvents = $eventJoined->where('status', 'registered')->count();
            $upcomingEvents = $eventJoined->where('status', 'pending')->count();
        @endphp
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon events">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Events</h3>
                    <div class="stat-value">{{ $eventJoined->count() }}</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon amount">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <h3>Total Spent</h3>
                    <div class="stat-value">RM {{ number_format($totalAmount, 2) }}</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>Active Events</h3>
                    <div class="stat-value">{{ $activeEvents }}</div>
                </div>
            </div>
            
        </div>

        <!-- Events Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-check"></i> Event</th>
                        <th><i class="fas fa-money-bill-wave"></i> Amount & Payment</th>
                        <th><i class="fas fa-tag"></i> Status</th>
                        <th><i class="fas fa-calendar-alt"></i> Dates</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventJoined as $joined)
                        @php
                            $event = $joined->event;
                            $payment = $joined->payment;
                            
                            // Determine status class based on joined status
                            $statusClass = match($joined->status) {
                                'registered' => 'registered',
                                'cancelled' => 'cancelled',
                                'pending' => 'pending',
                                default => 'completed'
                            };
                            
                            // Determine status icon
                            $statusIcon = match($joined->status) {
                                'registered' => 'check-circle',
                                'cancelled' => 'times-circle',
                                'pending' => 'clock',
                                default => 'calendar-check'
                            };
                        @endphp
                        
                        <tr>
                            <td>
                                <div class="event-info">
                                    <div class="event-name">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ $event->event_name }}
                                    </div>
                                    <div class="event-description">
                                        {{ Str::limit($event->event_description, 100) }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="amount">
                                    <i class="fas fa-money-bill-wave"></i>
                                    RM {{ number_format($payment->paymentAmount ?? 0, 2) }}
                                </div>
                                @if($payment->paymentMethod ?? false)
                                <div class="payment-method">
                                    <i class="fas fa-credit-card"></i>
                                    {{ $payment->paymentMethod }}
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="status {{ $statusClass }}">
                                    <i class="fas fa-{{ $statusIcon }}"></i>
                                    {{ ucfirst($joined->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="date">
                                    <div class="date-item">
                                        <i class="fas fa-calendar-plus"></i>
                                        <span>Joined: {{ $joined->joinedDate->format('M d, Y H:i') }}</span>
                                    </div>
                                    @if($event->event_start_date ?? false)
                                    <div class="date-item">
                                        <i class="fas fa-calendar-day"></i>
                                        <span>Event: {{ \Carbon\Carbon::parse($event->event_start_date)->format('M d, Y') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Page Footer -->
    <div class="page-footer">
        <p>© {{ date('Y') }} Sportify Events. All rights reserved.</p>
        <p>Need assistance with your events? <a href="mailto:support@sportify.com">Contact our support team</a></p>
    </div>
</div>
@endsection