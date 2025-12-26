@extends('layouts.app')

@section('title', 'Edit Equipment')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('inventory.show', $equipment->id) }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Equipment Details
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Edit Equipment</h1>
        <p class="mt-2 text-sm text-gray-600">Update equipment information</p>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <form action="{{ route('inventory.update', $equipment->id) }}" method="POST" class="divide-y divide-gray-200">
            @csrf
            @method('PUT')

            <div class="px-6 sm:px-8 py-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 gap-y-8 sm:grid-cols-2" style="gap: 2rem 2.5rem;">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-3">Equipment Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $equipment->name) }}" required
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-3">Status *</label>
                        <select name="status" id="status" required
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white">
                            <option value="available" {{ old('status', $equipment->status) == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ old('status', $equipment->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="damaged" {{ old('status', $equipment->status) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            <option value="retired" {{ old('status', $equipment->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-3">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">{{ old('description', $equipment->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="px-6 sm:px-8 py-8 bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Brand & Model</h2>
                <div class="grid grid-cols-1 gap-y-8 sm:grid-cols-2" style="gap: 2rem 2.5rem;">

                <div>
                    <label for="sport_type_id" class="block text-sm font-medium text-gray-700 mb-3">Sport Type *</label>
                    <select name="sport_type_id" id="sport_type_id" required
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white">
                        <option value="">Select Sport Type</option>
                        @foreach($sportTypes as $sportType)
                            <option value="{{ $sportType->id }}" {{ old('sport_type_id', $equipment->sport_type_id) == $sportType->id ? 'selected' : '' }}>
                                {{ $sportType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-3">Brand</label>
                    <select name="brand_id" id="brand_id"
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base bg-white">
                        <option value="">No Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $equipment->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                    <div class="sm:col-span-2">
                        <label for="model" class="block text-sm font-medium text-gray-700 mb-3">Model</label>
                        <input type="text" name="model" id="model" value="{{ old('model', $equipment->model) }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base"
                            placeholder="e.g., Fan Zhendong Super ZLC">
                    </div>
                </div>
            </div>

            <div class="px-6 sm:px-8 py-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Inventory Details</h2>
                <div class="grid grid-cols-1 gap-y-8 sm:grid-cols-2" style="gap: 2rem 2.5rem;">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-3">Total Quantity *</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $equipment->quantity) }}" min="0" required
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>

                    <div>
                        <label for="available_quantity" class="block text-sm font-medium text-gray-700 mb-3">Available Quantity *</label>
                        <input type="number" name="available_quantity" id="available_quantity" value="{{ old('available_quantity', $equipment->available_quantity) }}" min="0" required
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>

                    <div>
                        <label for="minimum_stock_amount" class="block text-sm font-medium text-gray-700 mb-3">Minimum Stock Amount</label>
                        <input type="number" name="minimum_stock_amount" id="minimum_stock_amount" value="{{ old('minimum_stock_amount', $equipment->minimum_stock_amount) }}" min="0"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                        <p class="mt-2 text-xs text-gray-500">Alert will trigger when available quantity falls below this amount</p>
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-3">Price</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $equipment->price) }}" step="0.01" min="0"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-3">Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location', $equipment->location) }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>

                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-3">Purchase Date</label>
                        <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>
                </div>
            </div>

            <div class="px-6 sm:px-8 py-8 bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Maintenance Information</h2>
                <div class="grid grid-cols-1 gap-y-8 sm:grid-cols-2" style="gap: 2rem 2.5rem;">
                    <div>
                        <label for="last_maintenance_date" class="block text-sm font-medium text-gray-700 mb-3">Last Maintenance Date</label>
                        <input type="date" name="last_maintenance_date" id="last_maintenance_date" value="{{ old('last_maintenance_date', $equipment->last_maintenance_date?->format('Y-m-d')) }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>

                    <div>
                        <label for="next_maintenance_date" class="block text-sm font-medium text-gray-700 mb-3">Next Maintenance Date</label>
                        <input type="date" name="next_maintenance_date" id="next_maintenance_date" value="{{ old('next_maintenance_date', $equipment->next_maintenance_date?->format('Y-m-d')) }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-3">Notes</label>
                        <textarea name="notes" id="notes" rows="4"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base">{{ old('notes', $equipment->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="px-6 sm:px-8 py-6 bg-white flex justify-end space-x-3">
                <a href="{{ route('inventory.show', $equipment->id) }}" class="inline-flex items-center justify-center px-6 py-3 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Equipment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

