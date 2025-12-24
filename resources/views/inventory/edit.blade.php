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

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('inventory.update', $equipment->id) }}" method="POST" class="space-y-6 p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Equipment Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $equipment->name) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" id="status" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="available" {{ old('status', $equipment->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="maintenance" {{ old('status', $equipment->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="damaged" {{ old('status', $equipment->status) == 'damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="retired" {{ old('status', $equipment->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $equipment->description) }}</textarea>
                </div>

                <div>
                    <label for="sport_type_id" class="block text-sm font-medium text-gray-700">Sport Type *</label>
                    <select name="sport_type_id" id="sport_type_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Sport Type</option>
                        @foreach($sportTypes as $sportType)
                            <option value="{{ $sportType->id }}" {{ old('sport_type_id', $equipment->sport_type_id) == $sportType->id ? 'selected' : '' }}>
                                {{ $sportType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="brand_id" class="block text-sm font-medium text-gray-700">Brand</label>
                    <select name="brand_id" id="brand_id"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">No Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $equipment->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                    <input type="text" name="model" id="model" value="{{ old('model', $equipment->model) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g., Fan Zhendong Super ZLC">
                </div>

                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Total Quantity *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $equipment->quantity) }}" min="0" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="available_quantity" class="block text-sm font-medium text-gray-700">Available Quantity *</label>
                    <input type="number" name="available_quantity" id="available_quantity" value="{{ old('available_quantity', $equipment->available_quantity) }}" min="0" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="minimum_stock_amount" class="block text-sm font-medium text-gray-700">Minimum Stock Amount</label>
                    <input type="number" name="minimum_stock_amount" id="minimum_stock_amount" value="{{ old('minimum_stock_amount', $equipment->minimum_stock_amount) }}" min="0"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Alert will trigger when available quantity falls below this amount</p>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $equipment->price) }}" step="0.01" min="0"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $equipment->location) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date</label>
                    <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date', $equipment->purchase_date?->format('Y-m-d')) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="last_maintenance_date" class="block text-sm font-medium text-gray-700">Last Maintenance Date</label>
                    <input type="date" name="last_maintenance_date" id="last_maintenance_date" value="{{ old('last_maintenance_date', $equipment->last_maintenance_date?->format('Y-m-d')) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="next_maintenance_date" class="block text-sm font-medium text-gray-700">Next Maintenance Date</label>
                    <input type="date" name="next_maintenance_date" id="next_maintenance_date" value="{{ old('next_maintenance_date', $equipment->next_maintenance_date?->format('Y-m-d')) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $equipment->notes) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('inventory.show', $equipment->id) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Update Equipment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

