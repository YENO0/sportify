@extends('layouts.app')

@section('title', $event->event_name)
@section('page-title', '')

@section('nav-links')
@endsection

@push('styles')
<style>
    /* Override layout background and container */
    body {
        background: #ffffff !important;
        padding: 0 !important;
    }

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

    .event-detail-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-top: 20px;
        margin-bottom: 20px;
        color: #667eea;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }

    .back-link:hover {
        text-decoration: underline;
    }

    .event-hero {
        margin-top: 30px;
        margin-bottom: 32px;
    }

    .event-hero-image {
        width: 100%;
        height: 420px;
        object-fit: cover;
        border-radius: 12px;
    }

    .event-hero-image-placeholder {
        width: 100%;
        height: 420px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 72px;
    }

    .event-hero-info {
        display: flex;
        flex-direction: column;
    }

    .event-badge {
        display: inline-block;
        padding: 6px 12px;
        background: #7c3aed;
        color: white;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 16px;
        width: fit-content;
    }

    .event-title-main {
        font-size: 36px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
        line-height: 1.2;
    }

    .event-organizer-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .event-organizer-header img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
    }

    .event-organizer-header .organizer-info {
        flex: 1;
    }

    .event-organizer-header .organizer-name {
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .event-organizer-header .follow-btn {
        padding: 6px 16px;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        color: #374151;
    }

    .event-meta-items {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }

    .event-meta-item {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #4b5563;
        font-size: 15px;
    }

    .event-meta-item svg {
        width: 20px;
        height: 20px;
        color: #6b7280;
    }

    .event-actions-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .event-action-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #d1d5db;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: white;
    }

    .event-price-box {
        background: #f9fafb;
        border-radius: 10px;
        padding: 24px;
        margin-bottom: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 20px rgba(17, 24, 39, 0.06);
    }

    .registration-pill {
        display: inline-block;
        padding: 8px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
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

    .event-price-main {
        font-size: 32px;
        font-weight: 700;
        color: #f97316;
        margin-bottom: 8px;
    }

    .event-price-main.free {
        color: #f97316;
    }

    .event-register-btn {
        width: 100%;
        padding: 16px;
        color: #333;
        background: white;
        border: 2px solid #999;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .event-register-btn:hover {
        border: 2px solid #333;
        transition: all 0.2s ease;
    }

    .event-register-btn:disabled {
        background: #9ca3af;
        cursor: not-allowed;
    }

    .event-body {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
        gap: 32px;
        margin-bottom: 60px;
    }

    .event-body-single {
        grid-template-columns: 1fr;
    }

    .event-main {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .event-side {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .tag-row {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .tag-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 12px;
        font-weight: 600;
    }

    .title-row {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .event-title-main {
        margin: 0;
    }

    .organizer-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 12px 0 20px;
    }

    .organizer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e5e7eb;
    }

    .organizer-name {
        font-weight: 600;
        color: #111827;
        font-size: 14px;
    }

    .organizer-subtext {
        font-size: 13px;
        color: #6b7280;
    }

    .meta-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #4b5563;
    }

    .meta-item svg {
        width: 18px;
        height: 18px;
        color: #6b7280;
    }

    .content-section {
        padding-bottom: 40px;
        border-bottom: 1px solid #e5e7eb;
    }

    .content-section:last-child {
        border-bottom: none;
    }

    .participants-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 12px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
    }

    .participants-table th,
    .participants-table td {
        padding: 12px 14px;
        text-align: left;
        font-size: 14px;
    }

    .participants-table thead {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .participants-empty {
        padding: 16px;
        color: #6b7280;
        font-size: 14px;
        background: #f9fafb;
        border-radius: 10px;
        border: 1px dashed #e5e7eb;
    }

    .participants-table tbody tr:nth-child(even) {
        background: #f9fafb;
    }

    .content-section-title {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 16px;
    }

    .content-section-text {
        font-size: 15px;
        line-height: 1.7;
        color: #374151;
        margin-bottom: 12px;
    }

    .read-more-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
    }

    .read-more-link:hover {
        text-decoration: underline;
    }

    .good-to-know-items {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .good-to-know-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 15px;
        color: #374151;
    }

    .good-to-know-item svg {
        width: 20px;
        height: 20px;
        color: #6b7280;
    }

    .location-address {
        font-size: 15px;
        color: #374151;
        margin-bottom: 16px;
    }

    .map-placeholder {
        width: 100%;
        height: 200px;
        background: #f3f4f6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        margin-bottom: 16px;
    }

    .show-map-btn {
        padding: 10px 20px;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        color: #374151;
    }

    .organizer-card {
        background: #f9fafb;
        border-radius: 8px;
        padding: 24px;
    }

    .organizer-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
    }

    .organizer-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #e5e7eb;
    }

    .organizer-stats {
        display: flex;
        gap: 16px;
        margin-bottom: 16px;
        font-size: 14px;
        color: #6b7280;
    }

    .organizer-stat {
        display: flex;
        flex-direction: column;
    }

    .organizer-stat-value {
        font-weight: 600;
        color: #111827;
    }

    .organizer-actions {
        display: flex;
        gap: 12px;
    }

    .organizer-btn {
        flex: 1;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        font-size: 14px;
        cursor: pointer;
        color: #374151;
    }

    .report-link {
        margin-top: 12px;
        text-align: center;
        font-size: 13px;
        color: #6b7280;
        text-decoration: none;
    }

    .report-link:hover {
        text-decoration: underline;
    }

    .few-slots-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        background: #f97316;
        color: white;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .event-action-icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #d1d5db;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: white;
        color: #6b7280;
        transition: all 0.2s ease;
    }

    .event-action-icon-btn:hover {
        background: #f9fafb;
        color: #374151;
    }

    /* Registration Modal Styles */
    .register-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .register-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .register-modal-content {
        position: relative;
        background: white;
        border-radius: 12px;
        max-width: 900px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        z-index: 1001;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .register-modal-left {
        padding: 32px;
        display: flex;
        flex-direction: column;
    }

    .register-modal-right {
        background: #f9fafb;
        padding: 32px;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .register-modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: white;
        color: #6b7280;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .register-modal-close:hover {
        background: #f3f4f6;
    }

    .register-modal-header {
        margin-bottom: 24px;
    }

    .register-modal-title {
        font-size: 24px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 8px;
    }

    .register-modal-date {
        font-size: 14px;
        color: #6b7280;
    }

    .register-ticket-box {
        border: 2px solid #3b82f6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 24px;
        background: white;
    }

    .register-ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 12px;
    }

    .register-ticket-type {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .register-ticket-price {
        font-size: 18px;
        font-weight: 700;
        color: #059669;
    }

    .register-ticket-note {
        font-size: 12px;
        color: #6b7280;
        margin-top: 8px;
    }

    .register-student-info {
        margin-bottom: 24px;
    }

    .register-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 16px;
    }

    .register-student-details {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .register-student-item {
        display: flex;
        justify-content: space-between;
        padding: 12px;
        background: #f9fafb;
        border-radius: 6px;
    }

    .register-student-label {
        font-size: 14px;
        color: #6b7280;
    }

    .register-student-value {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .register-modal-footer {
        margin-top: auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 24px;
        border-top: 1px solid #e5e7eb;
    }

    .register-modal-btn {
        padding: 12px 32px;
        background: #f97316;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .register-modal-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .register-modal-image-placeholder {
        width: 100%;
        height: 200px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 48px;
        margin-bottom: 24px;
    }

    .register-order-summary {
        background: white;
        border-radius: 8px;
        padding: 20px;
    }

    .register-order-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 16px;
    }

    .register-order-item {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
        color: #374151;
    }

    .register-order-total {
        display: flex;
        justify-content: space-between;
        padding: 16px 0 0;
        font-size: 16px;
        font-weight: 600;
        color: #111827;
    }

    /* Admin Actions Styles */
    .admin-actions-container {
        display: flex;
        flex-direction: column;
        gap: 16px;
        max-width: 820px;
        padding: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
    }

    .admin-action-form {
        margin: 0;
    }

    .admin-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        border: none;
        border-radius: 5px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
        letter-spacing: 0.01em;
    }

    .admin-btn-approve {
        background: #f97316;
        color: white;
    }

    .admin-btn-approve:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(37, 99, 235, 0.3);
    }

    .admin-btn-reject {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        white-space: nowrap;
        min-width: 160px;
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.22);
    }

    .admin-btn-reject:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(239, 68, 68, 0.28);
    }

    .admin-reject-form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .admin-reject-input-group {
        display: flex;
        gap: 12px;
        align-items: stretch;
    }

    .admin-reject-input {
        flex: 1;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s ease;
        min-width: 0;
        background: white;
    }

    .admin-reject-input:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .admin-status-box {
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        max-width: 820px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
    }

    .admin-status-rejected {
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-left: 4px solid #ef4444;
    }

    .admin-status-approved {
        background: linear-gradient(135deg, #ecfdf3 0%, #d1fae5 100%);
        border-left: 4px solid #10b981;
    }

    .admin-status-header {
        display: flex;
        align-items: center;
        font-weight: 700;
        margin-bottom: 12px;
        font-size: 15px;
        letter-spacing: 0.01em;
    }

    .admin-status-rejected .admin-status-header {
        color: #b91c1c;
    }

    .admin-status-approved .admin-status-header {
        color: #047857;
    }

    .admin-status-content {
        font-size: 14px;
        line-height: 1.6;
    }

    .admin-status-rejected .admin-status-content {
        color: #991b1b;
    }

    .admin-status-approved .admin-status-content {
        color: #065f46;
    }

    @media (max-width: 768px) {
        .admin-reject-input-group {
            flex-direction: column;
        }

        .admin-btn-reject {
            width: 100%;
            min-width: auto;
        }
    }

    @media (max-width: 1024px) {
        .event-hero {
            grid-template-columns: 1fr;
        }

        .event-content {
            grid-template-columns: 1fr;
        }

        .register-modal-content {
            grid-template-columns: 1fr;
            max-width: 100%;
        }

        .register-modal-right {
            order: -1;
        }
    }
</style>
@endpush

@section('content')
<div class="event-detail-page">
    <a href="{{ ($isAdminView ?? false) ? route('admin.events.index') : (($isCommitteeView ?? false) ? route('committee.events.index') : route('events.approved')) }}" class="back-link">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to events
    </a>
    @php
        $startDate = \Carbon\Carbon::parse($event->event_start_date);
        $endDate = $event->event_end_date ? \Carbon\Carbon::parse($event->event_end_date) : null;
        
        // Safely get committee name
        try {
            $committeeName = optional($event->committee)->name ?? 'Committee #' . $event->committee_id;
        } catch (\Exception $e) {
            $committeeName = 'Committee #' . $event->committee_id;
        }
        $registerDueDate = $event->registration_due_date ? \Carbon\Carbon::parse($event->registration_due_date) : null;
        $daysToDue = $registerDueDate ? now()->diffInDays($registerDueDate, false) : null;
        $showSalesEndSoon = $registerDueDate && $daysToDue !== null && $daysToDue <= 2 && $daysToDue >= 0;
        $registrationStatus = $event->registration_status ?? 'NotOpen';
        $registrationMap = [
            'NotOpen' => ['Coming Soon', true, 'notopen'],
            'Open' => ['Register Now', false, 'open'],
            'Full' => ['Sold Out', true, 'full'],
            'Closed' => ['Registration Closed', true, 'closed'],
        ];
        [$registrationText, $registrationDisabled, $registrationClass] = $registrationMap[$registrationStatus] ?? ['Coming Soon', true, 'notopen'];
    @endphp

    <!-- Event Hero Section -->
    <div class="event-hero">
        <div>
            @if($event->event_poster)
                <img src="{{ asset('storage/' . $event->event_poster) }}" alt="{{ $event->event_name }}" class="event-hero-image">
            @else
                <div class="event-hero-image-placeholder">📅</div>
            @endif
        </div>
    </div>

    <!-- Event Body -->
    <div class="event-body {{ (($isAdminView ?? false) || ($isCommitteeView ?? false)) ? '' : 'event-body-single' }}">
        <div class="event-main">
            <div class="tag-row">
                @if($showSalesEndSoon)
                    <span class="tag-pill" style="background:#fef3c7;color:#92400e;">Sales end soon</span>
                @endif
            </div>

            <div class="title-row">
                <h1 class="event-title-main">{{ $event->event_name }}</h1>
            </div>

            <div class="organizer-row">
                <div class="organizer-avatar"></div>
                <div>
                    <div class="organizer-name">By {{ $committeeName }}</div>
                    <div class="organizer-subtext">Organizer</div>
                </div>
            </div>

            <div class="meta-list">
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $startDate->format('M d') }} @if($endDate && !$startDate->isSameDay($endDate))- {{ $endDate->format('M d') }} @endif · {{ $startDate->format('g:i A') }} GMT+8</span>
                </div>
                @if($registerDueDate)
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Registration due: {{ $registerDueDate->format('M d, Y') }}</span>
                </div>
                @endif
                <div class="meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5a5 5 0 1110 0v1.25a2.25 2.25 0 01-2.25 2.25H9.25A2.25 2.25 0 017 12.75z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19h14"></path>
                    </svg>
                    <span>Capacity: {{ $event->max_capacity }}</span>
                </div>
            </div>

            <div class="content-section">
                <h2 class="content-section-title">Overview</h2>
                @if($event->event_description)
                    <p class="content-section-text">{{ $event->event_description }}</p>
                @else
                    <p class="content-section-text" style="color: #9ca3af;">No description available.</p>
                @endif
                <div style="margin-top: 16px;">
                    <span style="font-size: 14px; color: #6b7280;">Category: </span>
                    <span style="font-size: 14px; color: #374151;">Events</span>
                </div>
            </div>

            @if($isAdminView ?? false)
                <div class="content-section">
                    <h2 class="content-section-title">Event Review</h2>

                    {{-- Pending --}}
                    @if($event->status === 'pending')
                        <div class="admin-actions-container">

                            <div style="font-size:14px;color:#475569;">
                                Review the event details before making a decision.
                            </div>

                            {{-- Approve --}}
                            <form action="{{ route('events.approve', $event) }}" method="POST">
                                @csrf
                                <input type="hidden" name="approved_by" value="1">
                                <button type="submit" class="admin-btn admin-btn-approve">
                                    Approve Event
                                </button>
                            </form>

                            {{-- Reject --}}
                            <form action="{{ route('events.reject', $event) }}" method="POST" class="admin-reject-form">
                                @csrf
                                <input type="hidden" name="approved_by" value="1">

                                <textarea
                                    name="rejection_remark"
                                    class="admin-reject-input"
                                    rows="3"
                                    placeholder="Reason for rejection (required)"
                                    required
                                ></textarea>

                                <button
                                    type="submit"
                                    class="admin-btn admin-btn-reject"
                                    onclick="return confirm('Reject this event?')"
                                >
                                    Reject Event
                                </button>
                            </form>
                        </div>

                    {{-- Approved --}}
                    @elseif($event->status === 'approved')
                        <div class="admin-status-box admin-status-approved">
                            <div class="admin-status-header">Event Approved</div>
                            <div class="admin-status-content">
                                Approved on
                                {{ optional($event->approved_at)->format('M d, Y') ?? '-' }}
                            </div>
                        </div>

                    {{-- Rejected --}}
                    @elseif($event->status === 'rejected')
                        <div class="admin-status-box admin-status-rejected">
                            <div class="admin-status-header">Event Rejected</div>
                            <div class="admin-status-content">
                                <strong>Reason:</strong><br>
                                {{ $event->rejection_remark }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @if(($isCommitteeView ?? false) && isset($registrations))
                @php
                    $participantCount = $registrations->count();
                @endphp
                <div class="content-section">
                    <h2 class="content-section-title">Participants ({{ $participantCount }})</h2>
                    @if($participantCount > 0)
                        <table class="participants-table">
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
                                        <td>#{{ $reg->studentID }}</td>
                                        <td>
                                            <span class="badge badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($reg->joinedDate)->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="participants-empty">No participants registered yet.</div>
                    @endif
                </div>
            @endif
        </div>

        <div class="event-side">
            <div class="event-price-box">
                <div class="event-price-main {{ $event->price == 0 ? 'free' : '' }}">
                    {{ $event->price == 0 ? 'Free' : 'RM ' . number_format($event->price, 2) }}
                </div>
                <div style="color: #6b7280; font-size: 14px; margin-bottom: 16px;">
                    {{ $startDate->format('M d') }} · {{ $startDate->format('g:i A') }} GMT+8
                </div>
                @if($isAdminView ?? false)
                    <div style="padding: 16px; background: #f3f4f6; border-radius: 8px; text-align: center; color: #374151; font-weight: 600;">
                        Status: {{ ucfirst($event->status) }}
                    </div>
                @elseif($isCommitteeView ?? false)
                    <div style="padding: 16px; background: #f3f4f6; border-radius: 8px; text-align: center; color: #374151; font-weight: 600; margin-bottom: 12px;">
                        Status: {{ ucfirst($event->status) }}
                    </div>
                    <div style="padding: 12px; background: #eef2ff; border-radius: 8px; text-align: center; color: #4338ca; font-weight: 600;">
                        Remaining: {{ $remaining ?? 0 }} / {{ $event->max_capacity }}
                    </div>
                @elseif($event->event_status === 'completed')
                    <div style="padding: 16px; background: #f3f4f6; border-radius: 8px; text-align: center; color: #374151; font-weight: 600;">
                        Event Completed
                    </div>
                @elseif($event->event_status === 'cancelled')
                    <div style="padding: 16px; background: #fee2e2; border-radius: 8px; text-align: center; color: #b91c1c; font-weight: 700;">
                        Event Cancelled
                    </div>
                @elseif($event->status === 'approved')
                    <span class="registration-pill {{ $registrationClass }}">{{ $registrationText }}</span>
                    @if(!$registrationDisabled && $remaining > 0 && !$isRegistered && in_array($event->event_status, ['Upcoming', 'Ongoing']))
                        <button type="button" class="event-register-btn" onclick="openRegisterModal()">Register Now</button>
                    @elseif($isRegistered)
                        <button class="event-register-btn" disabled style="background: #10b981;">Registered</button>
                    @else
                        <button class="event-register-btn" disabled>{{ $registrationText }}</button>
                    @endif
                @else
                    <div style="padding: 16px; background: #fef3c7; border-radius: 8px; text-align: center; color: #92400e; font-weight: 600;">
                        Status: {{ ucfirst($event->status) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Registration Modal -->
@if($event->status === 'approved' && $event->registration_status === 'Open' && in_array($event->event_status, ['Upcoming', 'Ongoing']) && $remaining > 0 && !$isRegistered && !$isCommitteeView && !$isAdminView)
<div id="registerModal" class="register-modal" style="display: none;">
    <div class="register-modal-overlay" onclick="closeRegisterModal()"></div>
    <div class="register-modal-content">
        <div class="register-modal-left">
            <div class="register-modal-header">
                <h2 class="register-modal-title">{{ $event->event_name }}</h2>
                <p class="register-modal-date">
                    {{ $startDate->format('l, F d') }} - {{ $startDate->format('g') }}-{{ $endDate ? $endDate->format('g') : $startDate->copy()->addHours(3)->format('g') }}pm +08
                </p>
            </div>

            <div class="register-ticket-box">
                <div class="register-ticket-header">
                    <div>
                        <div class="register-ticket-type">General Admission</div>
                        <div class="register-ticket-price">{{ $event->price == 0 ? 'Free' : 'RM ' . number_format($event->price, 2) }}</div>
                    </div>
                </div>
                @if($daysLeft !== null && $daysLeft > 0)
                    <div class="register-ticket-note">Sales end in {{ $daysLeft }} {{ $daysLeft == 1 ? 'day' : 'days' }}</div>
                @endif
            </div>

            @if($student)
                <div class="register-student-info">
                    <h3 class="register-section-title">Your Details</h3>
                    <div class="register-student-details">
                        <div class="register-student-item">
                            <span class="register-student-label">Name:</span>
                            <span class="register-student-value">{{ $student->name }}</span>
                        </div>
                        <div class="register-student-item">
                            <span class="register-student-label">Email:</span>
                            <span class="register-student-value">{{ $student->email }}</span>
                        </div>
                        @if($student->course)
                            <div class="register-student-item">
                                <span class="register-student-label">Course:</span>
                                <span class="register-student-value">{{ $student->course }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="register-modal-footer">
                <div style="font-size: 12px; color: #6b7280;">Powered by Sportify</div>
                <button type="button" class="register-modal-btn" disabled>Register</button>
            </div>
        </div>

        <div class="register-modal-right">
            <button class="register-modal-close" onclick="closeRegisterModal()">×</button>
            @if($event->event_poster)
                <img src="{{ asset('storage/' . $event->event_poster) }}" alt="{{ $event->event_name }}" class="register-modal-image">
            @else
                <div class="register-modal-image-placeholder">📅</div>
            @endif
            <div class="register-order-summary">
                <h3 class="register-order-title">Order summary</h3>
                <div class="register-order-item">
                    <span>1 x General Admission</span>
                    <span>{{ $event->price == 0 ? 'RM 0.00' : 'RM ' . number_format($event->price, 2) }}</span>
                </div>
                <div class="register-order-total">
                    <span>Total</span>
                    <span>{{ $event->price == 0 ? 'RM 0.00' : 'RM ' . number_format($event->price, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    function openRegisterModal() {
        document.getElementById('registerModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeRegisterModal() {
        document.getElementById('registerModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeRegisterModal();
        }
    });
</script>
@endpush

@endsection

