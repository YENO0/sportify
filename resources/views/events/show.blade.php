@extends('layouts.app')

@section('title', 'Event Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Events
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $event->name }}</h1>
        <p class="mt-2 text-sm text-gray-600">Event Details</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Event Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Event Information</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Start Date & Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $event->start_date->format('M d, Y') }} at {{ is_string($event->start_time) ? $event->start_time : $event->start_time->format('H:i') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">End Date & Time</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $event->end_date->format('M d, Y') }} at {{ is_string($event->end_time) ? $event->end_time : $event->end_time->format('H:i') }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->location ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @php
                                $statusColors = [
                                    'upcoming' => 'bg-blue-100 text-blue-800',
                                    'ongoing' => 'bg-green-100 text-green-800',
                                    'completed' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $color = $statusColors[$event->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
                @if($event->description)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $event->description }}</dd>
                    </div>
                @endif
            </div>

            <!-- Borrowed Equipment -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Borrowed Equipment</h2>
                    @if(!$event->hasEnded())
                        <a href="{{ route('equipment-borrowings.create', $event) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                            Borrow Equipment
                        </a>
                    @endif
                </div>
                
                @if($event->equipmentBorrowings->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrowed At</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($event->equipmentBorrowings as $borrowing)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">
                                            <a href="{{ route('inventory.show', $borrowing->equipment_id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                                {{ $borrowing->equipment->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $borrowing->quantity }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($borrowing->isReturned())
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Returned
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Borrowed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $borrowing->borrowed_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if(!$borrowing->isReturned())
                                                <form action="{{ route('equipment-borrowings.destroy', [$event, $borrowing->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to return this equipment?')">
                                                        Return
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">Returned {{ $borrowing->returned_at->format('M d, Y H:i') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">No equipment borrowed for this event.</p>
                    @if(!$event->hasEnded())
                        <div class="text-center">
                            <a href="{{ route('equipment-borrowings.create', $event) }}" class="text-blue-600 hover:text-blue-900">
                                Borrow equipment for this event
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('events.edit', $event) }}" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 block text-center">
                        Edit Event
                    </a>
                    
                    @if($event->equipmentBorrowings->where('status', 'borrowed')->count() > 0 && !$event->hasEnded())
                        <form action="{{ route('events.testReturn', $event) }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-yellow-700" 
                                onclick="return confirm('This will set the event end date/time to the past and process automatic returns. Continue?')">
                                Test Return Logic
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Event Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Event Status</h2>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Event Status</span>
                        <span class="font-medium">
                            @if($event->hasEnded())
                                <span class="text-red-600">Ended</span>
                            @elseif($event->isOngoing())
                                <span class="text-green-600">Ongoing</span>
                            @else
                                <span class="text-blue-600">Upcoming</span>
                            @endif
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 mt-2">
                        @if($event->hasEnded())
                            Event ended on {{ $event->end_date->format('M d, Y') }} at {{ is_string($event->end_time) ? $event->end_time : $event->end_time->format('H:i') }}
                        @elseif($event->isOngoing())
                            Event is currently ongoing
                        @else
                            Event starts on {{ $event->start_date->format('M d, Y') }} at {{ is_string($event->start_time) ? $event->start_time : $event->start_time->format('H:i') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

