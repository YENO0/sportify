@extends('layouts.app')

@section('title', 'Event Details')
@section('page-title', 'Event Details & Participants')

@section('nav-links')
    <a href="{{ route('committee.events.index') }}" class="btn btn-secondary">← Back to Events</a>
@endsection

@section('content')
    @php
        $registered = $event->registrations_count ?? 0;
        $remaining = max(0, $event->max_capacity - $registered);
        $facilityNames = [
            1 => 'Main Hall',
            2 => 'Conference Room',
            3 => 'Outdoor Field',
        ];
    @endphp

    <div class="card">
        <h2 class="card-title">Event Information</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Event Name</div>
                <div class="info-value">{{ $event->event_name }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Start Date</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($event->event_start_date)->format('M d, Y') }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">End Date</div>
                <div class="info-value">{{ $event->event_end_date ? \Carbon\Carbon::parse($event->event_end_date)->format('M d, Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Registration Due</div>
                <div class="info-value">{{ $event->registration_due_date ? \Carbon\Carbon::parse($event->registration_due_date)->format('M d, Y') : 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Capacity</div>
                <div class="info-value">{{ $event->max_capacity }} people</div>
            </div>
            <div class="info-item">
                <div class="info-label">Registered</div>
                <div class="info-value">{{ $registered }} / {{ $event->max_capacity }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Remaining</div>
                <div class="info-value remaining {{ $remaining === 0 ? 'full' : ($remaining <= 5 ? 'low' : '') }}">{{ $remaining }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Price</div>
                <div class="info-value price">RM {{ number_format($event->price, 2) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Facility</div>
                <div class="info-value">{{ $facilityNames[$event->facility_id] ?? 'N/A' }}</div>
            </div>
        </div>

        @if($event->event_description)
            <div style="margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 6px;">
                <div class="info-label">Description</div>
                <p style="margin-top: 8px; color: #374151;">{{ $event->event_description }}</p>
            </div>
        @endif

        @if($event->status === 'rejected' && $event->rejection_remark)
            <div style="margin-top: 20px; padding: 15px; background: #fee2e2; border-left: 4px solid #ef4444; border-radius: 6px;">
                <div class="info-label" style="color: #991b1b;">Rejection Remark</div>
                <p style="margin-top: 8px; color: #991b1b;">{{ $event->rejection_remark }}</p>
            </div>
        @endif
    </div>

    <div class="card">
        <h2 class="card-title">Participants ({{ $registrations->count() }})</h2>
        @if($registrations->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Status</th>
                        <th>Joined Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registrations as $reg)
                        <tr>
                            <td><strong>#{{ $reg->studentID }}</strong></td>
                            <td>
                                <span class="badge badge-{{ $reg->status }}">
                                    {{ ucfirst($reg->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($reg->joinedDate)->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">👥</div>
                <p>No participants registered yet.</p>
            </div>
        @endif
    </div>
@endsection
