@extends('layouts.app')

@section('title', 'Add New Equipment')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add New Equipment</h1>
        <p class="mt-2 text-sm text-gray-600">Create equipment using Factory Method pattern</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('inventory.store') }}" method="POST" class="space-y-8 p-6">
            @csrf

            <!-- Section 1: Basic Information -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Equipment Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Equipment Type *</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="sports" {{ old('type') == 'sports' ? 'selected' : '' }}>Sports Equipment</option>
                            <option value="gym" {{ old('type') == 'gym' ? 'selected' : '' }}>Gym Equipment</option>
                            <option value="outdoor" {{ old('type') == 'outdoor' ? 'selected' : '' }}>Outdoor Equipment</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Factory Method pattern will create equipment based on type</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Brand Information -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Brand Information</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700">Brand</label>
                        <select name="brand_id" id="brand_id"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Brand (Optional)</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $selectedBrandId ?? null) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">
                            <a href="{{ route('brands.create') }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                Register a new brand
                            </a>
                        </p>
                    </div>

                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                        <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="e.g., Fan Zhendong Super ZLC"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Optional model name or variant</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Inventory Details -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Inventory Details</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="minimum_stock_amount" class="block text-sm font-medium text-gray-700">Minimum Stock Amount</label>
                    <input type="number" name="minimum_stock_amount" id="minimum_stock_amount" value="{{ old('minimum_stock_amount') }}" min="0"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Auto-set by Factory Method">
                    <p class="mt-1 text-xs text-gray-500">Factory Method will set default based on equipment type if left empty</p>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700">Purchase Date</label>
                        <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Section 4: Features (Decorator Pattern) -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Additional Features (Decorator Pattern)</h2>
                <p class="text-sm text-gray-600 mb-4">Add optional features to your equipment using the Decorator pattern</p>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="add_insurance" id="add_insurance" value="1" {{ old('add_insurance') ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm flex-1">
                            <label for="add_insurance" class="font-medium text-gray-700">Add Insurance Coverage</label>
                            <p class="text-gray-500">Decorator pattern: InsuranceDecorator</p>
                            <div class="mt-2 space-y-2" id="insurance-details" style="display: none;">
                                <input type="number" name="insurance_cost" id="insurance_cost" value="{{ old('insurance_cost') }}" step="0.01" min="0" placeholder="Insurance Cost"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <input type="date" name="insurance_expiry" id="insurance_expiry" value="{{ old('insurance_expiry') }}" placeholder="Expiry Date"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="add_warranty" id="add_warranty" value="1" {{ old('add_warranty') ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm flex-1">
                            <label for="add_warranty" class="font-medium text-gray-700">Add Warranty</label>
                            <p class="text-gray-500">Decorator pattern: WarrantyDecorator</p>
                            <div class="mt-2 space-y-2" id="warranty-details" style="display: none;">
                                <input type="text" name="warranty_type" id="warranty_type" value="{{ old('warranty_type', 'Standard') }}" placeholder="Warranty Type"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <input type="date" name="warranty_expiry" id="warranty_expiry" value="{{ old('warranty_expiry') }}" placeholder="Expiry Date"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="add_maintenance_tracking" id="add_maintenance_tracking" value="1" {{ old('add_maintenance_tracking') ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm flex-1">
                            <label for="add_maintenance_tracking" class="font-medium text-gray-700">Add Maintenance Tracking</label>
                            <p class="text-gray-500">Decorator pattern: MaintenanceTrackingDecorator</p>
                            <div class="mt-2" id="maintenance-details" style="display: none;">
                                <input type="number" name="maintenance_interval" id="maintenance_interval" value="{{ old('maintenance_interval', 3) }}" min="1" placeholder="Interval (months)"
                                    class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('inventory.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Create Equipment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('add_insurance').addEventListener('change', function() {
        document.getElementById('insurance-details').style.display = this.checked ? 'block' : 'none';
    });
    document.getElementById('add_warranty').addEventListener('change', function() {
        document.getElementById('warranty-details').style.display = this.checked ? 'block' : 'none';
    });
    document.getElementById('add_maintenance_tracking').addEventListener('change', function() {
        document.getElementById('maintenance-details').style.display = this.checked ? 'block' : 'none';
    });
    document.getElementById('add_low_stock_alert').addEventListener('change', function() {
        document.getElementById('low-stock-details').style.display = this.checked ? 'block' : 'none';
    });
    
    // Show if already checked
    if (document.getElementById('add_insurance').checked) {
        document.getElementById('insurance-details').style.display = 'block';
    }
    if (document.getElementById('add_warranty').checked) {
        document.getElementById('warranty-details').style.display = 'block';
    }
    if (document.getElementById('add_maintenance_tracking').checked) {
        document.getElementById('maintenance-details').style.display = 'block';
    }
    if (document.getElementById('add_low_stock_alert').checked) {
        document.getElementById('low-stock-details').style.display = 'block';
    }
</script>
@endsection
