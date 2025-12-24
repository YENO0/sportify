@extends('layouts.app')

@section('title', 'Schedule Maintenance')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('maintenance.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Maintenance Dashboard
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Schedule Maintenance</h1>
        <p class="mt-2 text-sm text-gray-600">Create maintenance record using Factory Method pattern</p>
    </div>

    <div class="bg-white shadow sm:rounded-lg">
        <form action="{{ route('maintenance.store') }}" method="POST" class="space-y-6 p-6">
            @csrf

            <!-- Section 1: Equipment Selection -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Equipment Selection</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="equipment_id" class="block text-sm font-medium text-gray-700">Equipment *</label>
                        <select name="equipment_id" id="equipment_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            onchange="updateEquipmentInfo()">
                            <option value="">Select Equipment</option>
                            @foreach($equipment as $item)
                                <option value="{{ $item->id }}" 
                                    data-available="{{ $item->available_quantity }}"
                                    {{ old('equipment_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }} 
                                    @if($item->brand)
                                        - {{ $item->brand->name }}
                                    @endif
                                    @if($item->model)
                                        ({{ $item->model }})
                                    @endif
                                    (Available: {{ $item->available_quantity }})
                                </option>
                            @endforeach
                        </select>
                        <p id="equipment-info" class="mt-1 text-xs text-gray-500"></p>
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity *</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}" min="1" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            onchange="validateQuantity()">
                        <p id="quantity-error" class="mt-1 text-xs text-red-600" style="display: none;"></p>
                        <p class="mt-1 text-xs text-gray-500">Quantity to be sent for maintenance</p>
                    </div>
                </div>
            </div>

            <!-- Section 2: Maintenance Details -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Maintenance Details</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="maintenance_type" class="block text-sm font-medium text-gray-700">Maintenance Type *</label>
                        <select name="maintenance_type" id="maintenance_type" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Type</option>
                            <option value="scheduled" {{ old('maintenance_type') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="emergency" {{ old('maintenance_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                            <option value="preventive" {{ old('maintenance_type') == 'preventive' ? 'selected' : '' }}>Preventive</option>
                            <option value="repair" {{ old('maintenance_type') == 'repair' ? 'selected' : '' }}>Repair</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Factory Method pattern will create maintenance based on type</p>
                    </div>

                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Scheduled Date *</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date', date('Y-m-d')) }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            onchange="updateDateValidation()">
                        <p class="mt-1 text-xs text-gray-500">When maintenance is scheduled</p>
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date *</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            onchange="updateDateValidation()">
                        <p class="mt-1 text-xs text-gray-500">When maintenance actually starts (quantity will be deducted)</p>
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date *</label>
                        <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            onchange="updateDateValidation()">
                        <p class="mt-1 text-xs text-gray-500">When maintenance ends (quantity will be returned)</p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g., Regular Service, Oil Change, Inspection">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Detailed description of the maintenance work">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Assignment & Cost -->
            <div class="border-b border-gray-200 pb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Assignment & Cost</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
                        <select name="assigned_to" id="assigned_to"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700">Estimated Cost</label>
                        <input type="number" name="cost" id="cost" value="{{ old('cost') }}" step="0.01" min="0"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0.00">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Additional notes or special instructions">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('maintenance.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Schedule Maintenance
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const equipmentData = @json($equipment->keyBy('id')->map(function($item) {
        return [
            'available_quantity' => $item->available_quantity,
            'name' => $item->name
        ];
    }));

    function updateEquipmentInfo() {
        const equipmentId = document.getElementById('equipment_id').value;
        const infoElement = document.getElementById('equipment-info');
        const quantityInput = document.getElementById('quantity');
        
        if (equipmentId && equipmentData[equipmentId]) {
            const available = equipmentData[equipmentId].available_quantity;
            infoElement.textContent = `Available Quantity: ${available}`;
            quantityInput.max = available;
            quantityInput.value = Math.min(parseInt(quantityInput.value) || 1, available);
            validateQuantity();
        } else {
            infoElement.textContent = '';
            quantityInput.max = '';
        }
    }

    function validateQuantity() {
        const equipmentId = document.getElementById('equipment_id').value;
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const errorElement = document.getElementById('quantity-error');
        
        if (equipmentId && equipmentData[equipmentId]) {
            const available = equipmentData[equipmentId].available_quantity;
            if (quantity > available) {
                errorElement.textContent = `Quantity cannot exceed available quantity (${available})`;
                errorElement.style.display = 'block';
                document.getElementById('quantity').setCustomValidity('Quantity exceeds available');
            } else {
                errorElement.style.display = 'none';
                document.getElementById('quantity').setCustomValidity('');
            }
        }
    }

    function updateDateValidation() {
        const scheduledDate = document.getElementById('scheduled_date').value;
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        
        if (scheduledDate) {
            startDate.min = scheduledDate;
            if (startDate.value && startDate.value < scheduledDate) {
                startDate.value = scheduledDate;
            }
        }
        
        if (startDate.value) {
            endDate.min = startDate.value;
            if (endDate.value && endDate.value <= startDate.value) {
                const nextDay = new Date(startDate.value);
                nextDay.setDate(nextDay.getDate() + 1);
                endDate.value = nextDay.toISOString().split('T')[0];
            }
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateEquipmentInfo();
        updateDateValidation();
    });
</script>
@endsection

