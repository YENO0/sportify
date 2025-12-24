@extends('layouts.app')

@section('title', 'Edit Event')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Event
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Event</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('events.update', $event) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Event Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $event->name) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $event->description) }}</textarea>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time *</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', is_string($event->start_time) ? $event->start_time : $event->start_time->format('H:i')) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time *</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', is_string($event->end_time) ? $event->end_time : $event->end_time->format('H:i')) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $event->location) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('events.show', $event) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Update Event
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

