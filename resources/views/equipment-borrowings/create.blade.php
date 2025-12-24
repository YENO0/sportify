@extends('layouts.app')

@section('title', 'Borrow Equipment for Event')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Event
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Borrow Equipment for Event</h1>
        <p class="mt-2 text-sm text-gray-600">Event: {{ $event->name }}</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('equipment-borrowings.store', $event) }}" method="POST" id="borrow-form">
            @csrf
            
            <div class="space-y-6">
                <!-- Equipment Selection Container -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-gray-700">Equipment *</label>
                        <button type="button" id="add-equipment-btn" class="text-sm bg-green-600 text-white px-3 py-1 rounded-md hover:bg-green-700">
                            + Add Equipment
                        </button>
                    </div>
                    
                    <div id="equipment-rows" class="space-y-4">
                        <!-- First row will be added by default -->
                    </div>
                    
                    <p class="mt-2 text-sm text-gray-500" id="no-equipment-message" style="display: none;">
                        Click "Add Equipment" to start borrowing equipment.
                    </p>
                    
                    @error('equipment.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('quantity.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('events.show', $event) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700">
                    Borrow Equipment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const equipmentRows = document.getElementById('equipment-rows');
    const addBtn = document.getElementById('add-equipment-btn');
    const noEquipmentMessage = document.getElementById('no-equipment-message');
    let rowCount = 0;
    const selectedEquipmentIds = new Set();
    
    // Equipment data from server
    const equipmentData = @json($equipment->map(function($eq) {
        return [
            'id' => $eq->id,
            'name' => $eq->name,
            'brand' => $eq->brand ? $eq->brand->name : null,
            'model' => $eq->model,
            'available' => $eq->available_quantity,
        ];
    })->values());
    
    // Borrowed equipment IDs
    const borrowedEquipmentIds = @json($borrowedEquipment);
    
    // Filter out already borrowed equipment
    const availableEquipment = equipmentData.filter(eq => !borrowedEquipmentIds.includes(eq.id) && eq.available > 0);
    
    function createEquipmentRow() {
        const rowId = `row-${rowCount++}`;
        const row = document.createElement('div');
        row.id = rowId;
        row.className = 'equipment-row border border-gray-300 rounded-lg p-4 bg-gray-50';
        
        row.innerHTML = `
            <div class="flex justify-between items-start mb-3">
                <h4 class="text-sm font-medium text-gray-700">Equipment #${rowCount}</h4>
                <button type="button" class="remove-row-btn text-red-600 hover:text-red-900 text-sm font-medium" onclick="removeRow('${rowId}')">
                    Remove
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Equipment *</label>
                    <select name="equipment[]" class="equipment-select block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Select Equipment</option>
                        ${availableEquipment.map(eq => `
                            <option value="${eq.id}" data-available="${eq.available}" data-name="${eq.name}">
                                ${eq.name}${eq.brand ? ' - ' + eq.brand : ''}${eq.model ? ' (' + eq.model + ')' : ''} - Available: ${eq.available}
                            </option>
                        `).join('')}
                    </select>
                    <p class="mt-1 text-xs text-red-600 error-message" style="display: none;"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="quantity[]" class="quantity-input block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" min="1" value="1" required>
                    <p class="mt-1 text-xs text-gray-500 available-hint">Select equipment first</p>
                </div>
            </div>
        `;
        
        equipmentRows.appendChild(row);
        noEquipmentMessage.style.display = 'none';
        
        // Add event listeners
        const select = row.querySelector('.equipment-select');
        const quantityInput = row.querySelector('.quantity-input');
        const hint = row.querySelector('.available-hint');
        const errorMsg = row.querySelector('.error-message');
        
        select.addEventListener('change', function() {
            const selectedId = this.value;
            const selectedOption = this.options[this.selectedIndex];
            
            // Check for duplicates
            if (selectedId && selectedEquipmentIds.has(selectedId)) {
                errorMsg.textContent = 'This equipment is already selected in another row.';
                errorMsg.style.display = 'block';
                this.value = '';
                quantityInput.value = 1;
                hint.textContent = 'Select equipment first';
                return;
            } else {
                errorMsg.style.display = 'none';
            }
            
            // Update selected equipment set
            updateSelectedEquipment();
            
            if (selectedId) {
                const available = parseInt(selectedOption.getAttribute('data-available'));
                quantityInput.max = available;
                quantityInput.value = Math.min(parseInt(quantityInput.value) || 1, available);
                hint.textContent = `Available: ${available}`;
                hint.className = 'mt-1 text-xs text-gray-500 available-hint';
            } else {
                quantityInput.max = '';
                quantityInput.value = 1;
                hint.textContent = 'Select equipment first';
                hint.className = 'mt-1 text-xs text-gray-500 available-hint';
            }
        });
        
        quantityInput.addEventListener('input', function() {
            const select = this.closest('.equipment-row').querySelector('.equipment-select');
            const selectedOption = select.options[select.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                const available = parseInt(selectedOption.getAttribute('data-available'));
                const requested = parseInt(this.value) || 0;
                
                if (requested > available) {
                    this.value = available;
                    hint.textContent = `Maximum available: ${available}`;
                    hint.className = 'mt-1 text-xs text-red-600 available-hint';
                } else if (requested < 1) {
                    this.value = 1;
                    hint.textContent = `Available: ${available}`;
                    hint.className = 'mt-1 text-xs text-gray-500 available-hint';
                } else {
                    hint.textContent = `Available: ${available}`;
                    hint.className = 'mt-1 text-xs text-gray-500 available-hint';
                }
            }
        });
    }
    
    function removeRow(rowId) {
        const row = document.getElementById(rowId);
        if (row) {
            const select = row.querySelector('.equipment-select');
            if (select && select.value) {
                selectedEquipmentIds.delete(select.value);
            }
            row.remove();
            updateSelectedEquipment();
            
            if (equipmentRows.children.length === 0) {
                noEquipmentMessage.style.display = 'block';
            }
        }
    }
    
    function updateSelectedEquipment() {
        selectedEquipmentIds.clear();
        document.querySelectorAll('.equipment-select').forEach(select => {
            if (select.value) {
                selectedEquipmentIds.add(select.value);
            }
        });
        
        // Check for duplicates and show errors
        document.querySelectorAll('.equipment-select').forEach(select => {
            if (select.value) {
                const count = Array.from(document.querySelectorAll('.equipment-select'))
                    .filter(s => s.value === select.value).length;
                
                const errorMsg = select.closest('.equipment-row').querySelector('.error-message');
                if (count > 1) {
                    errorMsg.textContent = 'This equipment is already selected in another row.';
                    errorMsg.style.display = 'block';
                } else {
                    errorMsg.style.display = 'none';
                }
            }
        });
    }
    
    // Add first row by default
    createEquipmentRow();
    
    // Add button click handler
    addBtn.addEventListener('click', function() {
        if (availableEquipment.length === 0) {
            alert('No equipment available for borrowing.');
            return;
        }
        
        if (selectedEquipmentIds.size >= availableEquipment.length) {
            alert('All available equipment has been selected.');
            return;
        }
        
        createEquipmentRow();
    });
    
    // Form submission validation
    document.getElementById('borrow-form').addEventListener('submit', function(e) {
        const selects = document.querySelectorAll('.equipment-select');
        const selectedIds = [];
        const duplicates = [];
        
        selects.forEach(select => {
            if (select.value) {
                if (selectedIds.includes(select.value)) {
                    duplicates.push(select.value);
                } else {
                    selectedIds.push(select.value);
                }
            }
        });
        
        if (duplicates.length > 0) {
            e.preventDefault();
            alert('Please remove duplicate equipment selections before submitting.');
            return false;
        }
        
        if (selectedIds.length === 0) {
            e.preventDefault();
            alert('Please select at least one equipment.');
            return false;
        }
        
        // Validate quantities
        let hasInvalidQuantity = false;
        selects.forEach(select => {
            if (select.value) {
                const row = select.closest('.equipment-row');
                const quantityInput = row.querySelector('.quantity-input');
                const available = parseInt(select.options[select.selectedIndex].getAttribute('data-available'));
                const requested = parseInt(quantityInput.value) || 0;
                
                if (requested > available || requested < 1) {
                    hasInvalidQuantity = true;
                }
            }
        });
        
        if (hasInvalidQuantity) {
            e.preventDefault();
            alert('Please ensure all quantities are valid and do not exceed available inventory.');
            return false;
        }
    });
    
    // Make removeRow available globally
    window.removeRow = removeRow;
});
</script>
@endsection
