@extends('layouts.app')

@section('title', 'Edit Equipment Borrowing')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('committee.events.show', $event->eventID) }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Event
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Equipment Borrowing</h1>
        <p class="mt-2 text-sm text-gray-600">Event: {{ $event->event_name }}</p>
        <p class="mt-1 text-sm text-gray-600">Equipment: {{ $equipment->name }}</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('equipment-borrowings.update', [$event, $borrowing]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity *</label>
                    <input type="number" 
                           name="quantity" 
                           id="quantity" 
                           value="{{ old('quantity', $borrowing->quantity) }}"
                           min="1" 
                           max="{{ $equipment->available_quantity + $borrowing->quantity }}"
                           required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">
                        Current quantity: {{ $borrowing->quantity }} | 
                        Available in inventory: {{ $equipment->available_quantity + $borrowing->quantity }}
                    </p>
                    @error('quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('committee.events.show', $event->eventID) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Update Borrowing
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

