@extends('layouts.app')

@section('title', 'Add New Sport Type')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('inventory.index', ['tab' => 'sport-types']) }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Add New Sport Type</h1>
        <p class="mt-2 text-sm text-gray-600">Create a new sport type for equipment categorization</p>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <form action="{{ route('sport-types.store') }}" method="POST" class="divide-y divide-gray-200">
            @csrf

            <div class="px-6 sm:px-8 py-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Sport Type Information</h2>
                <div class="grid grid-cols-1 gap-y-8 sm:grid-cols-2" style="gap: 2rem 2.5rem;">
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-3">Sport Type Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="e.g., Ping Pong, Football, Basketball">
                        <p class="mt-2 text-xs text-gray-500">The name of the sport type (e.g., Ping Pong, Football, Basketball)</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-3">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="Optional description of this sport type">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700 mb-3">Icon (Optional)</label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon') }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="Icon name or identifier">
                        <p class="mt-2 text-xs text-gray-500">For future use - icon identifier</p>
                    </div>

                    <div class="flex items-start pt-8">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3">
                            <label for="is_active" class="block text-sm font-medium text-gray-700">
                                Active
                            </label>
                            <p class="mt-1 text-xs text-gray-500">Only active sport types appear in equipment creation</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 sm:px-8 py-6 bg-white flex justify-end space-x-3">
                <a href="{{ route('inventory.index', ['tab' => 'sport-types']) }}" class="inline-flex items-center justify-center px-6 py-3 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    Create Sport Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

