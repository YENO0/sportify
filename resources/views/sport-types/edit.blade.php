@extends('layouts.app')

@section('title', 'Edit Sport Type')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('sport-types.show', $sportType->id) }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Sport Type Details
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Sport Type</h1>
        <p class="mt-2 text-sm text-gray-600">Update sport type information</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('sport-types.update', $sportType->id) }}" method="POST" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Sport Type Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $sportType->name) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., Ping Pong, Football, Basketball">
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Optional description of this sport type">{{ old('description', $sportType->description) }}</textarea>
                </div>

                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700">Icon (Optional)</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon', $sportType->icon) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Icon name or identifier">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $sportType->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('sport-types.show', $sportType->id) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Update Sport Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

