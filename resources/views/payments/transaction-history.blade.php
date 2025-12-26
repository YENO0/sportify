@extends('layouts.app')

@section('title', 'Transaction History')
@section('page-title', '')

@section('nav-links')
@endsection

@push('styles')
<<<<<<< HEAD
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    /* Override layout background and container */
=======
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    /* Override layout background (avoid changing global layout containers) */
>>>>>>> origin/main
    body {
        background: linear-gradient(135deg, #f4f6f8 0%, #e5e7eb 100%) !important;
        padding: 0 !important;
    }

<<<<<<< HEAD
    .container {
        max-width: 100% !important;
        margin: 0 !important;
        background: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 30px 20px !important;
    }

    .header {
        display: none !important;
    }

        :root {
            --primary-color: #6366f1;
            --primary-light: #818cf8;
            --secondary-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --dark-color: #1f2937;
            --gray-color: #6b7280;
            --light-gray: #f3f4f6;
            --white: #ffffff;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        }
=======
    :root {
        --primary-color: #6366f1;
        --primary-light: #818cf8;
        --secondary-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --dark-color: #1f2937;
        --gray-color: #6b7280;
        --light-gray: #f3f4f6;
        --white: #ffffff;
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --hover-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }
>>>>>>> origin/main

    .transactions-page {
        max-width: 1200px;
        margin: 0 auto;
        font-family: 'Poppins', sans-serif;
<<<<<<< HEAD
=======
        padding: 0;
    }

    /* Hide scrollbar for Chrome, Safari and Opera */
    .table-container::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    .table-container {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    /* Header Styles */
    .page-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 30px;
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
    }

    .page-header h1 {
        font-size: 2.5rem;
        color: var(--dark-color);
        margin-bottom: 10px;
        font-weight: 700;
    }

    .page-header p {
        color: var(--gray-color);
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Tabs (My Events / Transaction History) */
    .transactions-page .page-tabs {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 18px;
        flex-wrap: wrap;
    }

    .transactions-page .page-tab {
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

    .transactions-page .page-tab:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        background: rgba(99, 102, 241, 0.12);
    }

    .transactions-page .page-tab.active {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: var(--white);
        border-color: transparent;
    }

    .transactions-page .user-info {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--light-gray);
    }

    .transactions-page .user-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 1.5rem;
        font-weight: 600;
    }

    .transactions-page .user-details h3 {
        font-size: 1.2rem;
        margin-bottom: 5px;
    }

    .transactions-page .user-details p {
        color: var(--gray-color);
        font-size: 0.9rem;
        margin: 0;
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

    .stat-icon.total { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
    .stat-icon.amount { background: linear-gradient(135deg, #10b981, #34d399); }
    .stat-icon.events { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
    .stat-icon.users { background: linear-gradient(135deg, #ec4899, #f472b6); }

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

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1000px;
    }

    thead {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
    }

    th {
        padding: 20px;
        text-align: left;
        color: var(--white);
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
    }

    th:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 25%;
        height: 50%;
        width: 1px;
        background: rgba(255, 255, 255, 0.2);
    }

    th i {
        margin-right: 10px;
    }

    tbody tr {
        border-bottom: 1px solid var(--light-gray);
        transition: background-color 0.2s ease;
    }

    tbody tr:hover {
        background-color: rgba(99, 102, 241, 0.05);
    }

    tbody tr:last-child {
        border-bottom: none;
    }

    td {
        padding: 20px;
        color: var(--dark-color);
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
    .status.pending { 
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }
    .status.cancelled { 
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }
    .status.completed { 
        background: rgba(99, 102, 241, 0.1);
        color: #6366f1;
        border: 1px solid rgba(99, 102, 241, 0.3);
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
    }

    .event-description {
        color: var(--gray-color);
        font-size: 0.9rem;
        line-height: 1.4;
    }

    /* User Info Column */
    .user-column {
        min-width: 200px;
    }

    .user-info-cell {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .user-name {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 1rem;
    }

    .user-email {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--gray-color);
        font-size: 0.85rem;
        word-break: break-all;
    }

    .user-email i {
        color: var(--primary-color);
        font-size: 0.8rem;
    }

    /* User Avatar in table */
    .user-avatar-small {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #8b5cf6, #a78bfa);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }

    /* Amount */
    .amount {
        font-weight: 700;
        font-size: 1.2rem;
        color: var(--secondary-color);
    }

    /* Date */
    .date {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--gray-color);
    }

    .date i {
        color: var(--primary-color);
    }

    /* No Data State */
    .no-data {
        text-align: center;
        padding: 60px 20px;
    }

    .no-data-icon {
        font-size: 4rem;
        color: var(--light-gray);
        margin-bottom: 20px;
    }

    .no-data h3 {
        color: var(--gray-color);
        margin-bottom: 10px;
        font-size: 1.5rem;
    }

    .no-data p {
        color: var(--gray-color);
        max-width: 400px;
        margin: 0 auto 30px;
    }

    .primary-btn {
        display: inline-block;
        padding: 12px 30px;
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: var(--white);
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .primary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
    }

    /* Footer */
    .page-footer {
        text-align: center;
        padding: 20px;
        color: var(--gray-color);
        font-size: 0.9rem;
        border-top: 1px solid var(--light-gray);
        margin-top: 40px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2rem;
>>>>>>> origin/main
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .table-container::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .table-container {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        /* Header Styles */
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px;
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: var(--dark-color);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .page-header p {
            color: var(--gray-color);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid var(--light-gray);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .user-details h3 {
            font-size: 1.2rem;
            margin-bottom: 5px;
        }

        .user-details p {
            color: var(--gray-color);
            font-size: 0.9rem;
            margin: 0;
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

        .stat-icon.total { background: linear-gradient(135deg, #8b5cf6, #a78bfa); }
        .stat-icon.amount { background: linear-gradient(135deg, #10b981, #34d399); }
        .stat-icon.events { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .stat-icon.users { background: linear-gradient(135deg, #ec4899, #f472b6); }

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

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        thead {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        }

        th {
            padding: 20px;
            text-align: left;
            color: var(--white);
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
        }

        th:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        th i {
            margin-right: 10px;
        }

        tbody tr {
            border-bottom: 1px solid var(--light-gray);
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.05);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 20px;
            color: var(--dark-color);
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
        .status.pending { 
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .status.cancelled { 
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .status.completed { 
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
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
        }

        .event-description {
            color: var(--gray-color);
            font-size: 0.9rem;
            line-height: 1.4;
        }

        /* User Info Column */
        .user-column {
            min-width: 200px;
        }

        .user-info-cell {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 1rem;
        }

        .user-email {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-color);
            font-size: 0.85rem;
            word-break: break-all;
        }

        .user-email i {
            color: var(--primary-color);
            font-size: 0.8rem;
        }

        /* User Avatar in table */
        .user-avatar-small {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #8b5cf6, #a78bfa);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }

        /* Amount */
        .amount {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--secondary-color);
        }

        /* Date */
        .date {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray-color);
        }

        .date i {
            color: var(--primary-color);
        }

        /* No Data State */
        .no-data {
            text-align: center;
            padding: 60px 20px;
        }

        .no-data-icon {
            font-size: 4rem;
            color: var(--light-gray);
            margin-bottom: 20px;
        }

        .no-data h3 {
            color: var(--gray-color);
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .no-data p {
            color: var(--gray-color);
            max-width: 400px;
            margin: 0 auto 30px;
        }

        .primary-btn {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: var(--white);
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .primary-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }

        /* Footer */
        .page-footer {
            text-align: center;
            padding: 20px;
            color: var(--gray-color);
            font-size: 0.9rem;
            border-top: 1px solid var(--light-gray);
            margin-top: 40px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            th, td {
                padding: 15px 10px;
            }
            
            .table-container {
                border-radius: 12px;
            }
        }

        @media (max-width: 480px) {
            .user-info {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Print Styles */
        @media print {
            .page-header, .stats-container, .page-footer {
                display: none;
            }
            
            .table-container {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            table {
                min-width: auto;
            }
        }
    </style>
@endpush

@section('content')
<div class="transactions-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="fas fa-history"></i> Transaction History</h1>
        <p>View all payment transactions and event registrations</p>
        @if(($user->isStudent() ?? false))
            <div class="page-tabs">
                <a class="page-tab" href="{{ route('payments.my-events') }}">
                    <i class="fas fa-calendar-check"></i>
                    My Events
                </a>
                <a class="page-tab active" href="{{ route('payments.transaction-history') }}">
                    <i class="fas fa-history"></i>
                    Transaction History
                </a>
            </div>
        @endif
        
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr($user->name ?? 'User', 0, 1)) }}
            </div>
            <div class="user-details">
                <h3>{{ $user->name ?? 'User' }}</h3>
                <p><i class="fas fa-envelope"></i> {{ $user->email ?? 'user@example.com' }}</p>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    @if(!$transactions->isEmpty())
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon total">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-content">
                <h3>Total Transactions</h3>
                <div class="stat-value">{{ $transactions->count() }}</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon amount">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-content">
                <h3>Total Amount</h3>
                <div class="stat-value">RM {{ number_format($transactions->sum('paymentAmount'), 2) }}</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon events">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h3>Events Attended</h3>
                @php
                    $uniqueEvents = $transactions->map(function($transaction) {
                        return $transaction->eventJoined->event->eventID ?? null;
                    })->filter()->unique()->count();
                @endphp
                <div class="stat-value">{{ $uniqueEvents }}</div>
            </div>
        </div>

        @if($user->role === 'committee')
        <div class="stat-card">
            <div class="stat-icon users">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>Unique Users</h3>
                @php
                    $uniqueUsers = $transactions->map(function($transaction) {
                        return $transaction->eventJoined->studentID ?? null;
                    })->filter()->unique()->count();
                @endphp
                <div class="stat-value">{{ $uniqueUsers }}</div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Transactions Table -->
    <div class="table-container">
        @if($transactions->isEmpty())
            <div class="no-data">
                <div class="no-data-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3>No Transactions Found</h3>
                <p>You haven't made any payments yet. Register for events to see your transaction history here.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-calendar-check"></i> Event</th>
                        <th><i class="fas fa-info-circle"></i> Description</th>
                        @if($user->role === 'committee')
                        <th class="user-column"><i class="fas fa-user"></i> User</th>
                        @endif
                        <th><i class="fas fa-money-bill-wave"></i> Amount</th>
                        <th><i class="fas fa-tag"></i> Status</th>
                        <th><i class="fas fa-calendar-alt"></i> Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        @php
                            $joined = $transaction->eventJoined;
                            $event = $joined->event ?? null;
                            $invoice = $joined->invoice ?? null;
                            
                            // Get user details for committee view
                            $transactionUser = $joined->user ?? null;
                        @endphp
                        <tr>
                            <td>
                                <div class="event-info">
                                    <div class="event-name">{{ $event->event_name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="event-description">
                                    {{ Str::limit($event->event_description ?? 'No description available', 80) }}
                                </div>
                            </td>
                            
                            @if($user->role === 'committee')
                            <td class="user-column">
                                @if($transactionUser)
                                <div class="user-info-cell">
                                    <div>
                                        <div class="user-name">{{ $transactionUser->name ?? 'Unknown User' }}</div>
                                        <div class="user-email">
                                            <i class="fas fa-envelope"></i>
                                            {{ $transactionUser->email ?? 'No email' }}
                                        </div>
                                    </div>
                                </div>
                                @else
                                <div class="user-info-cell">
                                    <div class="user-avatar-small">
                                        ?
                                    </div>
                                    <div>
                                        <div class="user-name">User Not Found</div>
                                        <div class="user-email">
                                            <i class="fas fa-envelope"></i>
                                            N/A
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                            @endif
                            
                            <td>
                                <div class="amount">RM {{ number_format($transaction->paymentAmount, 2) }}</div>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($joined->status ?? '') {
                                        'registered' => 'registered',
                                        'cancelled' => 'cancelled',
                                        'pending' => 'pending',
                                        default => 'completed'
                                    };
                                @endphp
                                <span class="status {{ $statusClass }}">
                                    <i class="fas fa-{{ $statusClass === 'registered' ? 'check-circle' : ($statusClass === 'cancelled' ? 'times-circle' : 'clock') }}"></i>
                                    {{ ucfirst($joined->status ?? 'Completed') }}
                                </span>
                            </td>
                            <td>
                                <div class="date">
                                    <i class="fas fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($joined->joinedDate ?? $transaction->paymentDate)->format('M d, Y') }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Page Footer -->
    <div class="page-footer">
        <p>© {{ date('Y') }} Sportify Events. All rights reserved.</p>
        <p>Need help? <a href="mailto:support@sportify.com" style="color: var(--primary-color); text-decoration: none;">Contact Support</a></p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth hover effect for table rows
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.01)';
                this.style.transition = 'transform 0.2s ease';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    });
</script>
@endpush
