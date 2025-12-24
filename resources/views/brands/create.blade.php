@extends('layouts.app')

@section('title', 'Register New Brand')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('brands.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Brands
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Register New Brand</h1>
        <p class="mt-2 text-sm text-gray-600">Add a new equipment brand to the system</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('brands.store') }}" method="POST" class="space-y-6 p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Brand Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., Butterfly">
                    <p class="mt-1 text-xs text-gray-500">The official brand name</p>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Brief description about the brand">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700">Website</label>
                    <input type="url" name="website" id="website" value="{{ old('website') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="https://example.com">
                </div>

                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                    <input type="email" name="contact_email" id="contact_email" value="{{ old('contact_email') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="contact@brand.com">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('brands.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Register Brand
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

