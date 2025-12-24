@extends('layouts.app')

@section('title', 'Add New Sport Type')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('sport-types.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Sport Types
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Add New Sport Type</h1>
        <p class="mt-2 text-sm text-gray-600">Create a new sport type for equipment categorization</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('sport-types.store') }}" method="POST" class="space-y-6 p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Sport Type Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., Ping Pong, Football, Basketball">
                    <p class="mt-1 text-xs text-gray-500">The name of the sport type (e.g., Ping Pong, Football, Basketball)</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Optional description of this sport type">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700">Icon (Optional)</label>
                    <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Icon name or identifier">
                    <p class="mt-1 text-xs text-gray-500">For future use - icon identifier</p>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        Active
                    </label>
                    <p class="ml-2 text-xs text-gray-500">Only active sport types appear in equipment creation</p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('sport-types.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Create Sport Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

