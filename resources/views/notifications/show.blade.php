@extends('layouts.app')

@section('title', 'Notification Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-blue-600 text-white p-4 rounded-t-lg flex items-center">
        <a href="{{ route('notifications.index') }}" class="text-white mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h17" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold">{{ $notification->data['title'] ?? 'Notification Details' }}</h1>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-b-lg p-6">
        <div class="mb-4">
            <h2 class="text-xl font-semibold text-gray-800">{{ $notification->data['title'] ?? 'Event Cancelled' }}</h2>
            <p class="text-gray-600 text-sm">Sender: {{ $notification->data['sender'] ?? 'System' }}</p>
            <p class="text-gray-600 text-sm">Date: {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}</p>
        </div>

        <div class="mb-6 border-t border-gray-200 pt-4">
            <p class="text-gray-700 leading-relaxed">{{ $notification->data['message'] ?? 'No message content available.' }}</p>
        </div>

        @if(isset($notification->data['facility_name']))
            <div class="mb-4">
                <p class="font-semibold text-gray-800">Facility: {{ $notification->data['facility_name'] }}</p>
            </div>
        @endif

        @if(isset($notification->data['closure_date']))
            <div class="mb-4">
                <p class="font-semibold text-gray-800">Closure Date: {{ $notification->data['closure_date'] }}</p>
            </div>
        @endif

        @if(isset($notification->data['action_url']))
            @php
                // Only show "Reapply" button for committee notifications
                // Students should NEVER see the "Reapply" button
                $isCommitteeNotification = isset($notification->data['notification_type']) && 
                    $notification->data['notification_type'] === 'event_cancelled_committee';
                $isStudent = auth()->check() && auth()->user()->isStudent();
            @endphp
            
            @if($isCommitteeNotification && !$isStudent)
                {{-- Only committees see the "Reapply" button --}}
                <div class="mt-6 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
                    <a href="{{ $notification->data['action_url'] }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        👉 Click here to Reapply an Alternative Event
                    </a>
                </div>
            @elseif(!$isStudent && isset($notification->data['action_url']))
                {{-- For other notification types (non-student users), show a generic action button if needed --}}
                <div class="mt-6">
                    <a href="{{ $notification->data['action_url'] }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        View Details
                    </a>
                </div>
            @endif
            {{-- Students with action_url will not see any button (intentional) --}}
        @endif
    </div>
</div>
@endsection