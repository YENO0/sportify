@extends('layouts.app')

@section('title', 'Approved Events')
@section('page-title', '')
@section('mainClass', 'max-w-none mx-0 py-0 px-0')

@section('nav-links')
@endsection

@push('styles')
<style>
    /* Override layout background and container */
    body {
        background: #f8fafc !important;
        padding: 0 !important;
    }

    .card {
        max-width: none;
        width: 100%;
        min-height: 100vh;
        border-radius: 0;
        padding: 0;
        background: #f8fafc;
        border: none;
        box-shadow: none;
    }

    /* Match User Management header layout */
    .page-header {
        background: #ffffff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        padding: 2rem 2.5rem;
    }
    .page-header h1 { font-size: 1.8rem; font-weight: 700; color: #1f2937; margin: 0; }
    .page-header p { font-size: 1rem; color: #4b5563; margin-top: 0.25rem; }
    .content-area { padding: 2.5rem; max-width: 1400px; margin: 0 auto; }

    .container {
        max-width: 100% !important;
        margin: 0 !important;
        background: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .header {
        display: none !important;
    }

    .events-header {
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
        padding: 0;
        margin-bottom: 30px;
    }

    .events-header-title {
        font-size: 32px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 24px;
    }

    .events-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 30px;
    }

    .events-tab {
        padding: 12px 24px;
        background: none;
        border: none;
        font-size: 15px;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
        position: relative;
        text-decoration: none;
    }

    .events-tab:hover {
        color: #374151;
    }

    .events-tab.active {
        color: #667eea;
        border-bottom-color: #667eea;
    }

    .events-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 40px;
        margin-top: 20px;
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
        padding: 0;
    }

    @media (max-width: 1100px) {
        .events-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 1100px) {
        .events-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 900px) {
        .events-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .events-grid {
            grid-template-columns: 1fr;
        }
    }

    .event-card-link {
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .event-card {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        border: 1px solid #e5e7eb;
        height: 100%;
    }

    .event-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .event-image-container {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .event-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .event-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 48px;
    }

    .event-status-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(255, 255, 255, 0.95);
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .event-status-badge.going-fast {
        background: #fef3c7;
        color: #92400e;
    }

    .event-status-badge.full {
        background: #fee2e2;
        color: #991b1b;
    }

    .event-card-content {
        padding: 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .event-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
        line-height: 1.4;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .event-description {
        font-size: 13px;
        color: #6b7280;
        line-height: 1.5;
        margin-bottom: 12px;
        flex: 1;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .event-details {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }

    .event-detail-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #4b5563;
    }

    .event-detail-item svg {
        width: 16px;
        height: 16px;
        color: #6b7280;
        flex-shrink: 0;
    }

    .event-detail-item strong {
        color: #111827;
        font-weight: 500;
    }

    .event-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid #e5e7eb;
        margin-top: auto;
    }

    .event-organizer {
        font-size: 12px;
        color: #6b7280;
    }

    .event-organizer strong {
        color: #111827;
        font-weight: 500;
    }

    .event-price {
        font-size: 16px;
        font-weight: 700;
        color: #059669;
    }

    .event-price.free {
        color: #667eea;
    }

    .event-capacity {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }

    .event-capacity.remaining-low {
        color: #f59e0b;
        font-weight: 600;
    }

    .event-capacity.remaining-full {
        color: #ef4444;
        font-weight: 600;
    }

    .registration-pill {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .registration-pill.notopen {
        background: #eef2ff;
        color: #4338ca;
    }

    .registration-pill.open {
        background: #ecfdf3;
        color: #047857;
    }

    .registration-pill.full {
        background: #fee2e2;
        color: #b91c1c;
    }

    .registration-pill.closed {
        background: #fef3c7;
        color: #92400e;
    }

    .register-cta {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 11px;
        min-width: 100px;
        text-align: center;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .register-cta.enabled {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
    }

    .register-cta.enabled:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(79, 70, 229, 0.25);
    }

    .register-cta.disabled {
        background: #f3f4f6;
        color: #9ca3af;
        pointer-events: none;
    }

    .event-register-row {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 12px;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #6b7280;
        max-width: 1100px;
        margin: 0 auto;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .empty-state h3 {
        font-size: 20px;
        color: #111827;
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
        margin-top: 8px;
    }

    .note-box {
        margin-top: 30px;
        padding: 15px;
        background: #f9fafb;
        border-radius: 8px;
        font-size: 12px;
        color: #6b7280;
        border-left: 4px solid #667eea;
        max-width: 1100px;
        margin-left: auto;
        margin-right: auto;
        padding-left: 20px;
        padding-right: 20px;
    }

    .note-box strong {
        color: #111827;
    }

</style>
@endpush

@section('content')
<div class="card">
    <header class="page-header">
        <h1>Events</h1>
        <p>Browse events and register to participate.</p>
    </header>

    <div class="content-area">
        <div class="events-header">
            <div class="events-tabs">
                <a href="{{ route('events.approved', ['filter' => 'all']) }}" class="events-tab {{ ($filter ?? 'all') === 'all' ? 'active' : '' }}">
                    All
                </a>
                <a href="{{ route('events.approved', ['filter' => 'this_week']) }}" class="events-tab {{ ($filter ?? 'all') === 'this_week' ? 'active' : '' }}">
                    This week
                </a>
                <a href="{{ route('events.approved', ['filter' => 'this_month']) }}" class="events-tab {{ ($filter ?? 'all') === 'this_month' ? 'active' : '' }}">
                    This month
                </a>
            </div>
        </div>

        @if($events->count() > 0)
        <div class="events-grid">
            @foreach($events as $event)
                @php
                    $registered = $event->registrations_count ?? 0;
                    $remaining = max(0, $event->max_capacity - $registered);
                    $remainingClass = $remaining === 0 ? 'remaining-full' : ($remaining <= 5 ? 'remaining-low' : '');
                    $statusBadge = $remaining === 0 ? 'full' : ($remaining <= 5 ? 'going-fast' : '');
                    $registrationStatus = $event->registration_status ?? 'NotOpen';
                    $registrationMap = [
                        'NotOpen' => ['Coming Soon', true, 'notopen'],
                        'Open' => ['Register Now', false, 'open'],
                        'Full' => ['Sold Out', true, 'full'],
                        'Closed' => ['Registration Closed', true, 'closed'],
                    ];
                    [$registrationText, $registrationDisabled, $registrationClass] = $registrationMap[$registrationStatus] ?? ['Coming Soon', true, 'notopen'];
                    
                    // Safely get committee name - handle case where user table doesn't exist yet
                    try {
                        $committeeName = optional($event->committee)->name ?? 'Committee #' . $event->committee_id;
                    } catch (\Exception $e) {
                        $committeeName = 'Committee #' . $event->committee_id;
                    }
                    
                    $startDate = \Carbon\Carbon::parse($event->event_start_date);
                    $endDate = $event->event_end_date ? \Carbon\Carbon::parse($event->event_end_date) : null;
                @endphp

                <a href="{{ route('events.show', $event) }}" class="event-card-link">
                <div class="event-card">
                    <div class="event-image-container">
                        @if($event->event_poster)
                            <img src="{{ asset('storage/' . $event->event_poster) }}" alt="{{ $event->event_name }}" class="event-image">
                        @else
                            <div class="event-image-placeholder">📅</div>
                        @endif
                        @if($statusBadge)
                            <div class="event-status-badge {{ $statusBadge }}">
                                {{ $remaining === 0 ? 'Full' : 'Going fast' }}
                            </div>
                        @endif
                    </div>

                    <div class="event-card-content">
                        <h3 class="event-title">{{ $event->event_name }}</h3>
                        <div class="event-details">
                            <div class="event-detail-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>
                                    <strong>{{ $startDate->format('D, M d') }}</strong>
                                    @if($endDate && !$startDate->isSameDay($endDate))
                                        - <strong>{{ $endDate->format('M d') }}</strong>
                                    @endif
                                </span>
                            </div>

                            <div class="event-detail-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span><strong>{{ $committeeName }}</strong></span>
                            </div>

                            <div class="event-detail-item">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span>
                                    <strong>{{ $remaining }}</strong> remaining
                                    <span class="event-capacity {{ $remainingClass }}">({{ $event->max_capacity }} total)</span>
                                </span>
                            </div>
                        </div>

                        <div class="event-meta">
                            <div>
                                <div class="event-organizer">
                                    By <strong>{{ $committeeName }}</strong>
                                </div>
                                <div class="event-capacity {{ $remainingClass }}">
                                    {{ $remaining }} spots left
                                </div>
                            </div>
                            <div class="event-price {{ $event->price == 0 ? 'free' : '' }}">
                                {{ $event->price == 0 ? 'Free' : 'RM ' . number_format($event->price, 2) }}
                            </div>
                        </div>

                        <div class="event-register-row">
                            <span class="register-cta {{ $registrationDisabled ? 'disabled' : 'enabled' }}">
                                {{ $registrationText }}
                            </span>
                        </div>

                    </div>
                </div>
                </a>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <h3>No events available</h3>
            <p>No approved events available at the moment.</p>
            <p style="margin-top: 10px; font-size: 14px;">Check back later for new events!</p>
        </div>
        @endif
    </div>
</div>

@endsection
