@extends('layouts.app')

@section('title', 'Events')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-3xl font-bold text-gray-900">Events</h1>
            <p class="mt-2 text-sm text-gray-700">Manage events and equipment borrowings</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <a href="{{ route('events.create') }}" class="block rounded-md bg-blue-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                Create New Event
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 bg-white p-4 rounded-lg shadow">
        <form method="GET" action="{{ route('events.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..." 
                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="min-w-[150px]">
                <select name="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Filter
            </button>
            <a href="{{ route('events.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Clear
            </a>
        </form>
    </div>

    <!-- Events Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        @if($events->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed Items</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($events as $event)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('events.show', $event) }}" class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                    {{ $event->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $event->start_date->format('M d, Y') }}<br>
                                <span class="text-gray-500">{{ is_string($event->start_time) ? $event->start_time : $event->start_time->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $event->end_date->format('M d, Y') }}<br>
                                <span class="text-gray-500">{{ is_string($event->end_time) ? $event->end_time : $event->end_time->format('H:i') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $event->location ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $event->equipmentBorrowings->where('status', 'borrowed')->count() }} item(s)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('events.edit', $event) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="px-6 py-4">
                {{ $events->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <p class="text-gray-500">No events found.</p>
                <a href="{{ route('events.create') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-900">
                    Create your first event
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

