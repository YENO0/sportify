@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">My Bookings</h1>

    <div class="flex justify-end mb-4">
        <a href="{{ route('bookings.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">New Booking</a>
    </div>

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-max w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Facility</th>
                    <th class="py-3 px-6 text-left">Start Time</th>
                    <th class="py-3 px-6 text-left">End Time</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @foreach($bookings as $booking)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">
                            {{ $booking->facility->name }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $booking->start_time }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $booking->end_time }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            @if(strtolower($booking->status) == 'cancelled')
                                <span class="bg-red-200 text-red-600 py-1 px-3 rounded-full text-xs">{{ $booking->status }}</span>
                            @else
                                <span class="bg-purple-200 text-purple-600 py-1 px-3 rounded-full text-xs">{{ $booking->status }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-6 text-center">
                            @if($booking->status != 'cancelled')
                                <form action="{{ route('bookings.destroy', $booking->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 text-white py-1 px-3 rounded-full text-xs">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
