@extends('layouts.app')

@section('title', 'Admin - Events')
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

    .events-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0;
        overflow: visible;
    }

    .events-header {
        margin-bottom: 30px;
    }

    .events-title {
        font-size: 32px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 20px;
    }

    .events-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 20px;
        text-decoration: none;
    }

    .events-tab {
        padding: 12px 20px;
        background: none;
        border: none;
        font-size: 15px;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .events-tab.active {
        color: #667eea;
        border-bottom-color: #667eea;
    }

    .events-tab:hover {
        color: #374151;
    }

    .events-actions {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .search-input {
        flex: 1;
        min-width: 200px;
        padding: 10px 16px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        background: white;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .search-wrapper {
        position: relative;
        flex: 1;
        min-width: 200px;
    }

    .view-toggle {
        display: flex;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        overflow: hidden;
    }

    .view-toggle-btn {
        padding: 8px 12px;
        background: white;
        border: none;
        cursor: pointer;
        color: #6b7280;
        transition: all 0.2s ease;
    }

    .view-toggle-btn.active {
        background: #667eea;
        color: white;
    }

    .view-toggle-btn svg {
        width: 18px;
        height: 18px;
    }

    .status-filter {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
    }

    .events-table {
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        overflow: visible;
    }

    .events-table table {
        width: 100%;
        border-collapse: collapse;
        overflow: visible;
    }

    .events-table thead {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .events-table th {
        padding: 12px 16px;
        text-align: left;
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .events-table td {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: middle;
        position: relative;
        overflow: visible;
    }

    .events-table tbody tr {
        position: relative;
        overflow: visible;
    }

    .events-table tbody tr:hover {
        background: #f9fafb;
    }

    .events-table tbody tr:last-child td {
        border-bottom: none;
    }

    .event-item {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .event-date-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        background: #f3f4f6;
        border-radius: 6px;
        font-weight: 600;
    }

    .event-date-month {
        font-size: 11px;
        color: #6b7280;
        text-transform: uppercase;
    }

    .event-date-day {
        font-size: 20px;
        color: #111827;
    }

    .event-image {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }

    .event-image-placeholder {
        width: 80px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
    }

    .event-info {
        flex: 1;
    }

    .event-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .event-datetime {
        font-size: 13px;
        color: #6b7280;
    }

    .event-committee {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 4px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-badge.pending {
        background: #fef3c7;
        color: #92400e;
    }

    .status-badge.approved {
        background: #d1fae5;
        color: #065f46;
    }

    .status-badge.rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-badge.full {
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-badge.draft {
        background: #f3f4f6;
        color: #4b5563;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .btn-approve {
        padding: 6px 12px;
        background: #10b981;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-approve:hover {
        background: #059669;
    }

    .btn-reject {
        padding: 6px 12px;
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-reject:hover {
        background: #dc2626;
    }

    .rejection-form {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .rejection-input {
        padding: 6px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 12px;
        min-width: 200px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .empty-state-icon {
        font-size: 48px;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')
<div class="card">
    <header class="page-header">
        <h1>Admin - Events</h1>
        <p>Review and manage event submissions and approvals.</p>
    </header>

    <div class="content-area">
        <div class="events-page">
            <div class="events-header">
                <div class="events-tabs">
            <a href="{{ route('admin.events.index', ['tab' => 'pending']) }}" class="events-tab {{ ($tab ?? 'pending') === 'pending' ? 'active' : '' }}">Events</a>
            <a href="{{ route('admin.events.index', ['tab' => 'approved']) }}" class="events-tab {{ ($tab ?? 'pending') === 'approved' ? 'active' : '' }}">Approved Events</a>
        </div>

        <form method="GET" action="{{ route('admin.events.index') }}" class="events-actions">
            <div class="search-wrapper">
                <input type="text" name="search" class="search-input" placeholder="Search events" value="{{ request('search') }}">
            </div>


            @if(($tab ?? 'pending') === 'pending')
                <select name="status" class="status-filter" onchange="this.form.submit()">
                    <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            @endif
        </form>
    </div>

            <div class="events-table">
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Committee</th>
                    <th>People Joined</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $showEvents = ($tab ?? 'pending') === 'approved' ? ($approvedEvents ?? []) : ($pendingEvents ?? []);
                    $emptyText = ($tab ?? 'pending') === 'approved' ? 'No approved events.' : 'No events pending approval.';
                @endphp
                @forelse($showEvents as $event)
                    @php
                        $registered = $event->registrations_count ?? 0;
                        $startDate = \Carbon\Carbon::parse($event->event_start_date);
                        
                        // Safely get committee name
                        try {
                            $committeeName = optional($event->committee)->name ?? 'Committee #' . $event->committee_id;
                        } catch (\Exception $e) {
                            $committeeName = 'Committee #' . $event->committee_id;
                        }
                    @endphp
                    <tr>
                        <td>
                            <a href="{{ route('events.show', ['event' => $event, 'admin' => 1]) }}" style="text-decoration: none; color: inherit;">
                                <div class="event-item">
                                    <div class="event-date-box">
                                        <div class="event-date-month">{{ $startDate->format('M') }}</div>
                                        <div class="event-date-day">{{ $startDate->format('d') }}</div>
                                    </div>
                                    @if($event->event_poster)
                                        <img src="{{ asset('storage/' . $event->event_poster) }}" alt="{{ $event->event_name }}" class="event-image">
                                    @else
                                        <div class="event-image-placeholder"></div>
                                    @endif
                                    <div class="event-info">
                                        <div class="event-title">{{ $event->event_name }}</div>
                                        <div class="event-datetime">
                                            {{ $startDate->format('l, F d, Y') }} at {{ $startDate->format('g:i A') }}
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <div class="event-committee">{{ $committeeName }}</div>
                        </td>
                        <td>{{ $registered }}/{{ $event->max_capacity }}</td>
                        <td>
                            <span class="status-badge {{ $event->status }}">{{ ucfirst($event->status) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="empty-state">
                            <p>{{ $emptyText }}</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
            </div>
        </div>
    </div>
</div>
@endsection

