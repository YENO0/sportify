@extends('layouts.app')

@section('title', 'Events')
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
        padding: 10px 16px 10px 40px;
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

    .search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #9ca3af;
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

    .create-event-btn {
        padding: 10px 20px;
        background: #f97316;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .create-event-btn:hover {
        background: #ea580c;
    }

    .events-table {
        background: white;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        overflow: visible !important;
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

    .dropdown-menu {
        position: relative;
    }

    .dropdown-toggle {
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dropdown-toggle:hover {
        color: #111827;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        min-width: 150px;
        z-index: 1000;
        margin-top: 4px;
    }

    .dropdown-menu.active .dropdown-content {
        display: block;
    }

    .dropdown-item {
        display: block;
        padding: 10px 16px;
        color: #374151;
        text-decoration: none;
        font-size: 14px;
        transition: background 0.2s ease;
    }

    .dropdown-item:hover {
        background: #f9fafb;
    }

    .dropdown-item.delete {
        color: #ef4444;
    }

    .dropdown-item.delete:hover {
        background: #fee2e2;
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

    .csv-export {
        margin-top: 20px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
    }

    .csv-export:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="card">
    <header class="page-header">
        <h1>Events</h1>
        <p>Manage your event applications and track their statuses.</p>
    </header>

    <div class="content-area">
        <div class="events-page">
            <div class="events-header">
                <div class="events-tabs">
            <button class="events-tab active">Events</button>
            <button class="events-tab">Collections</button>
        </div>

        <form method="GET" action="{{ route('committee.events.index') }}" class="events-actions">
            <div class="search-wrapper">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" name="search" class="search-input" placeholder="Search events" value="{{ request('search') }}">
            </div>

            <div class="view-toggle">
                <button type="button" class="view-toggle-btn active">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </button>
                <button type="button" class="view-toggle-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </button>
            </div>

            <select name="status" class="status-filter" onchange="this.form.submit()">
                <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Full</option>
            </select>

            <a href="{{ route('events.create') }}" class="create-event-btn">Apply Event</a>
        </form>
    </div>

            <div class="events-table">
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>People Joined</th>
                    <th>Gross</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    @php
                        $registered = $event->registrations_count ?? 0;
                        $startDate = \Carbon\Carbon::parse($event->event_start_date);
                    @endphp
                    <tr>
                        <td>
                            <div class="event-item">
                                <div class="event-date-box">
                                    <div class="event-date-month">{{ $startDate->format('M') }}</div>
                                    <div class="event-date-day">{{ $startDate->format('d') }}</div>
                                </div>
                                @if($event->event_poster)
                                    <img src="{{ asset('storage/' . $event->event_poster) }}" alt="{{ $event->event_name }}" class="event-image">
                                @else
                                    <div class="event-image-placeholder">📅</div>
                                @endif
                                <div class="event-info">
                                    <div class="event-title">{{ $event->event_name }}</div>
                                    <div class="event-datetime">
                                        {{ $startDate->format('l, F d, Y') }} at {{ $startDate->format('g:i A') }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $registered }}/{{ $event->max_capacity }}</td>
                        <td>RM {{ number_format($event->price * $registered, 2) }}</td>
                        <td>
                            @php
                                $eventStatus = $event->status ?? 'draft';
                            @endphp
                            <span class="status-badge {{ $eventStatus }}">{{ ucfirst($eventStatus) }}</span>
                        </td>
                        <td>
                            <div class="dropdown-menu" id="dropdown-{{ $event->id }}">
                                <button class="dropdown-toggle" data-event-id="{{ $event->id }}">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                                <div class="dropdown-content">
                                    <a href="{{ route('committee.events.show', $event) }}" class="dropdown-item">View Event</a>
                                    @if($event->status !== 'approved')
                                        <a href="{{ route('events.edit', $event) }}" class="dropdown-item">Edit</a>
                                    @endif
                                    @if($event->status === 'draft')
                                        <form action="{{ route('events.update', $event) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="apply_event" value="1">
                                            <button type="submit" class="dropdown-item" style="width: 100%; text-align: left; border: none; background: none; cursor: pointer;">Apply Event</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('events.destroy', $event) }}" method="POST" style="margin: 0;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item delete" style="width: 100%; text-align: left; border: none; background: none; cursor: pointer;">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="empty-state">
                            <div class="empty-state-icon">📅</div>
                            <p>No events found. Create your first event!</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
            </div>

    <a href="#" class="csv-export">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        CSV Export
    </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle dropdown toggle using event delegation
    document.addEventListener('click', function(event) {
        const toggle = event.target.closest('.dropdown-toggle');
        
        if (toggle) {
            event.stopPropagation();
            const eventId = toggle.getAttribute('data-event-id');
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== 'dropdown-' + eventId) {
                    menu.classList.remove('active');
                }
            });
            
            // Toggle current dropdown
            const menu = document.getElementById('dropdown-' + eventId);
            if (menu) {
                menu.classList.toggle('active');
            }
        } else {
            // Close dropdown when clicking outside
            const isInsideDropdown = event.target.closest('.dropdown-menu');
            if (!isInsideDropdown) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('active');
                });
            }
        }
    });
</script>
@endpush
@endsection
