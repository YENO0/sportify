@extends('layouts.app')

@section('title', 'Add New Equipment')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        <!-- Page Header -->
    <div class="mb-8">
            <a href="{{ route('inventory.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
                ← Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Add New Equipment</h1>
            <p class="text-sm text-gray-600">Create equipment using Factory Method pattern</p>
    </div>

        <!-- Form Container -->
        <div class="bg-white shadow-lg rounded-xl overflow-hidden">
            <form action="{{ route('inventory.store') }}" method="POST" class="space-y-0" enctype="multipart/form-data">
            @csrf

            <!-- Section 1: Basic Information -->
                <div class="border-b border-gray-200 px-6 sm:px-8 py-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Basic Information</h2>
                        <p class="text-sm text-gray-500">Provide the essential details about the equipment</p>
                    </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Equipment Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                    </div>

                    <div>
                        <label for="sport_type_id" class="block text-sm font-medium text-gray-700 mb-2">Sport Type *</label>
                        <select name="sport_type_id" id="sport_type_id" required
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out bg-white">
                            <option value="">Select Sport Type</option>
                            @foreach($sportTypes as $sportType)
                                <option value="{{ $sportType->id }}" {{ old('sport_type_id', $selectedSportTypeId) == $sportType->id ? 'selected' : '' }}>
                                    {{ $sportType->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500">
                            Factory Method pattern will create equipment based on sport type.
                            <a href="{{ route('sport-types.create') }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">Add new sport type</a>
                        </p>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out resize-none">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Brand Information -->
            <div class="border-b border-gray-200 px-6 sm:px-8 py-8 bg-gray-50">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Brand Information</h2>
                    <p class="text-sm text-gray-500">Specify the brand and model details</p>
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                        <select name="brand_id" id="brand_id"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out bg-white">
                            <option value="">Select Brand (Optional)</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $selectedBrandId ?? null) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-gray-500">
                            <a href="{{ route('brands.create') }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">
                                Register a new brand
                            </a>
                        </p>
                    </div>

                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700 mb-2">Model</label>
                        <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="e.g., Fan Zhendong Super ZLC"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                        <p class="mt-2 text-xs text-gray-500">Optional model name or variant</p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Images (Decorator Pattern) -->
            <div class="border-b border-gray-200 px-6 sm:px-8 py-8">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Equipment Images</h2>
                    <p class="text-sm text-gray-500">Upload at least one image of the equipment. Images will be processed using Decorator Pattern.</p>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-3">Images * (Multiple allowed)</label>
                        <div class="flex flex-col sm:flex-row sm:items-start gap-4">
                            <label for="images" class="cursor-pointer inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Select Images
                            </label>
                            <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" 
                                class="hidden"
                                onchange="previewImages(this)">
                            <span id="file-count" class="text-sm text-gray-600 font-medium px-4 py-2 bg-gray-100 rounded-lg">No images selected</span>
                        </div>
                        <p class="mt-3 text-xs text-gray-500 bg-blue-50 px-3 py-2 rounded-md border border-blue-100">
                            <span class="font-medium">Allowed formats:</span> JPEG, JPG, PNG, GIF, WEBP. <span class="font-medium">Maximum size:</span> 5MB per image. You can select multiple images at once.
                        </p>
                        
                        <!-- Image Preview Section -->
                        <div id="image-preview-container" class="mt-8 p-6 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200" style="display: none;">
                            <h3 class="text-sm font-semibold text-gray-700 mb-4">Selected Images (Hover to remove)</h3>
                            <div id="image-preview" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4"></div>
                            <div id="image-alt-texts" class="mt-6 space-y-4"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 4: Inventory Details -->
            <div class="border-b border-gray-200 px-6 sm:px-8 py-8 bg-gray-50">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Inventory Details</h2>
                    <p class="text-sm text-gray-500">Manage stock levels and equipment information</p>
                </div>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" required
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                </div>

                <div>
                    <label for="minimum_stock_amount" class="block text-sm font-medium text-gray-700 mb-2">Minimum Stock Amount</label>
                    <input type="number" name="minimum_stock_amount" id="minimum_stock_amount" value="{{ old('minimum_stock_amount') }}" min="0"
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out"
                        placeholder="Auto-set by Factory Method">
                    <p class="mt-2 text-xs text-gray-500">Factory Method will set default based on sport type if left empty</p>
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                    <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0"
                        class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                    </div>

                    <div>
                        <label for="purchase_date" class="block text-sm font-medium text-gray-700 mb-2">Purchase Date</label>
                        <input type="date" name="purchase_date" id="purchase_date" value="{{ old('purchase_date') }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out bg-white">
                    </div>
                </div>
            </div>

            <!-- Section 5: Features (Decorator Pattern) -->
            <div class="border-b border-gray-200 px-6 sm:px-8 py-8">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">Additional Features (Decorator Pattern)</h2>
                    <p class="text-sm text-gray-500">Add optional features to your equipment using the Decorator pattern</p>
                </div>
                
                <div class="space-y-6">
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                        <div class="flex items-center h-5 pt-0.5">
                            <input type="checkbox" name="add_insurance" id="add_insurance" value="1" {{ old('add_insurance') ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-6 flex-1">
                            <label for="add_insurance" class="block font-semibold text-gray-900 cursor-pointer">Add Insurance Coverage</label>
                            <p class="text-sm text-gray-500 mt-1">Decorator pattern: InsuranceDecorator</p>
                            <div class="mt-4 space-y-3" id="insurance-details" style="display: none;">
                                <input type="number" name="insurance_cost" id="insurance_cost" value="{{ old('insurance_cost') }}" step="0.01" min="0" placeholder="Insurance Cost"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                                <input type="date" name="insurance_expiry" id="insurance_expiry" value="{{ old('insurance_expiry') }}" placeholder="Expiry Date"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out bg-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                        <div class="flex items-center h-5 pt-0.5">
                            <input type="checkbox" name="add_warranty" id="add_warranty" value="1" {{ old('add_warranty') ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-4 flex-1">
                            <label for="add_warranty" class="block font-semibold text-gray-900 cursor-pointer">Add Warranty</label>
                            <p class="text-sm text-gray-500 mt-1">Decorator pattern: WarrantyDecorator</p>
                            <div class="mt-4 space-y-3" id="warranty-details" style="display: none;">
                                <input type="text" name="warranty_type" id="warranty_type" value="{{ old('warranty_type', 'Standard') }}" placeholder="Warranty Type"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                                <input type="date" name="warranty_expiry" id="warranty_expiry" value="{{ old('warranty_expiry') }}" placeholder="Expiry Date"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out bg-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                        <div class="flex items-center h-5 pt-0.5">
                            <input type="checkbox" name="add_maintenance_tracking" id="add_maintenance_tracking" value="1" {{ old('add_maintenance_tracking') ? 'checked' : '' }}
                                class="focus:ring-blue-500 h-5 w-5 text-blue-600 border-gray-300 rounded">
                        </div>
                        <div class="ml-6 flex-1">
                            <label for="add_maintenance_tracking" class="block font-semibold text-gray-900 cursor-pointer">Add Maintenance Tracking</label>
                            <p class="text-sm text-gray-500 mt-1">Decorator pattern: MaintenanceTrackingDecorator</p>
                            <div class="mt-4" id="maintenance-details" style="display: none;">
                                <input type="number" name="maintenance_interval" id="maintenance_interval" value="{{ old('maintenance_interval', 3) }}" min="1" placeholder="Interval (months)"
                                    class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 sm:px-8 py-6 bg-white flex justify-end space-x-3">
                <a href="{{ route('inventory.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-red-300 rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Equipment
                </button>
            </div>
        </form>
    </div>
    </div>
</div>

<script>
    // Store selected files globally - MUST be declared before any functions use them
    var selectedFiles = [];
    var fileDataUrls = {};

    // Image preview functionality - defined before DOMContentLoaded so it's available for inline handlers
    function previewImages(input) {
        console.log('previewImages called', input.files);
        
        const preview = document.getElementById('image-preview');
        const altTextsContainer = document.getElementById('image-alt-texts');
        const previewContainer = document.getElementById('image-preview-container');
        const fileCount = document.getElementById('file-count');
        
        if (!input || !input.files || input.files.length === 0) {
            console.log('No files selected');
            if (selectedFiles.length === 0) {
                if (previewContainer) previewContainer.style.display = 'none';
                if (fileCount) fileCount.textContent = 'No images selected';
                return;
            }
        } else {
            // Add new files to existing selection
            const newFiles = Array.from(input.files);
            console.log('New files:', newFiles.length);
            
            newFiles.forEach(file => {
                // Create unique key for file
                const fileKey = file.name + '_' + file.size + '_' + file.lastModified;
                
                // Check if file already exists
                const exists = selectedFiles.some(f => {
                    const key = f.name + '_' + f.size + '_' + f.lastModified;
                    return key === fileKey;
                });
                
                if (!exists) {
                    selectedFiles.push(file);
                    console.log('Added file:', file.name);
                }
            });
        }

        // Update file count
        if (fileCount) {
            fileCount.textContent = `${selectedFiles.length} image(s) selected`;
        }

        // Clear and rebuild preview
        if (preview) preview.innerHTML = '';
        if (altTextsContainer) altTextsContainer.innerHTML = '';

        if (selectedFiles.length === 0) {
            if (previewContainer) previewContainer.style.display = 'none';
            if (fileCount) fileCount.textContent = 'No images selected';
            return;
        }

        console.log('Processing', selectedFiles.length, 'files');

        // Show preview container immediately
        if (previewContainer) {
            previewContainer.style.display = 'block';
            console.log('Showing preview container immediately');
        }

        // Create preview for all selected files
        let processedCount = 0;
        selectedFiles.forEach((file, index) => {
            // Validate file
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            
            if (file.size > maxSize) {
                alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
                removeImage(index);
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                alert(`File "${file.name}" has invalid type. Allowed: JPEG, PNG, GIF, WEBP.`);
                removeImage(index);
                return;
            }

            // Create preview
            const reader = new FileReader();
            reader.onload = function(e) {
                processedCount++;
                const fileKey = file.name + '_' + file.size + '_' + file.lastModified;
                const dataUrl = e.target.result;
                fileDataUrls[fileKey] = dataUrl;
                
                // Verify data URL is valid
                if (!dataUrl || !dataUrl.startsWith('data:image/')) {
                    console.error('Invalid data URL for file:', file.name);
                    alert(`Error loading preview for "${file.name}". Please try again.`);
                    return;
                }
                
                const div = document.createElement('div');
                div.className = 'relative group';
                div.setAttribute('data-file-key', fileKey);
                
                // Create image container
                const imgContainer = document.createElement('div');
                imgContainer.className = 'aspect-square bg-gray-100 rounded-lg border-2 border-gray-300 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative';
                
                // Create image element programmatically (not via innerHTML)
                const img = document.createElement('img');
                img.src = dataUrl;
                img.alt = `Preview ${index + 1}`;
                img.className = 'w-full h-full object-cover';
                img.style.display = 'block';
                img.onerror = function() {
                    console.error('Image failed to load:', file.name, 'DataURL length:', dataUrl ? dataUrl.length : 0);
                    img.style.display = 'none';
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'w-full h-full flex items-center justify-center text-red-500 text-sm bg-gray-100';
                    errorDiv.textContent = 'Failed to load';
                    imgContainer.appendChild(errorDiv);
                };
                img.onload = function() {
                    console.log('Image loaded successfully:', file.name, 'Dimensions:', this.width, 'x', this.height);
                };
                imgContainer.appendChild(img);
                
                // Add overlay (positioned absolutely, behind badge)
                const overlay = document.createElement('div');
                overlay.className = 'absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none';
                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.onclick = function(e) { e.stopPropagation(); removeImageByKey(fileKey); };
                removeBtn.className = 'bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 pointer-events-auto transition duration-150 ease-in-out shadow-md';
                removeBtn.textContent = 'Remove';
                overlay.appendChild(removeBtn);
                imgContainer.appendChild(overlay);
                
                // Add number badge (positioned on top)
                const badge = document.createElement('span');
                badge.className = 'absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-semibold shadow z-10';
                badge.textContent = index + 1;
                imgContainer.appendChild(badge);
                
                div.appendChild(imgContainer);
                if (preview) preview.appendChild(div);

                // Create alt text input
                const altDiv = document.createElement('div');
                altDiv.className = 'flex items-start gap-3';
                altDiv.setAttribute('data-file-key', fileKey);
                altDiv.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 w-28 pt-2.5">Image ${index + 1}:</label>
                    <input type="text" name="image_alt_texts[]" value="${file.name.replace(/\.[^/.]+$/, '')}" 
                        class="flex-1 block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out text-sm"
                        placeholder="Alt text for image ${index + 1}">
                `;
                if (altTextsContainer) altTextsContainer.appendChild(altDiv);

                // Log when image is loaded
                if (processedCount === selectedFiles.length) {
                    console.log('All images loaded');
                }
            };
            
            reader.onerror = function() {
                console.error('Error reading file:', file.name);
                alert(`Error reading file "${file.name}". Please try again.`);
            };
            
            reader.readAsDataURL(file);
        });

        // Update the file input to maintain selected files
        updateFileInput();
    }

    function removeImageByKey(fileKey) {
        // Find and remove file from selectedFiles
        const index = selectedFiles.findIndex(file => {
            const key = file.name + '_' + file.size + '_' + file.lastModified;
            return key === fileKey;
        });
        
        if (index !== -1) {
            selectedFiles.splice(index, 1);
            delete fileDataUrls[fileKey];
            
            // Rebuild preview
            rebuildPreview();
        }
    }

    function removeImage(index) {
        if (index >= 0 && index < selectedFiles.length) {
            selectedFiles.splice(index, 1);
            rebuildPreview();
        }
    }

    function rebuildPreview() {
        // Clear preview
        const preview = document.getElementById('image-preview');
        const altTextsContainer = document.getElementById('image-alt-texts');
        const previewContainer = document.getElementById('image-preview-container');
        const fileCount = document.getElementById('file-count');
        
        if (preview) preview.innerHTML = '';
        if (altTextsContainer) altTextsContainer.innerHTML = '';
        fileDataUrls = {};

        // Rebuild from selectedFiles
        const input = document.getElementById('images');
        updateFileInput();
        
        if (selectedFiles.length === 0) {
            if (previewContainer) previewContainer.style.display = 'none';
            if (fileCount) fileCount.textContent = 'No images selected';
            return;
        }
        
        // Manually trigger preview rebuild
        let processedCount = 0;
        selectedFiles.forEach((file, index) => {
            const maxSize = 5 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            
            if (file.size > maxSize || !allowedTypes.includes(file.type)) {
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                processedCount++;
                const fileKey = file.name + '_' + file.size + '_' + file.lastModified;
                const dataUrl = e.target.result;
                fileDataUrls[fileKey] = dataUrl;
                
                // Verify data URL is valid
                if (!dataUrl || !dataUrl.startsWith('data:image/')) {
                    console.error('Invalid data URL for file:', file.name);
                    return;
                }
                
                const div = document.createElement('div');
                div.className = 'relative group';
                div.setAttribute('data-file-key', fileKey);
                
                // Create image container
                const imgContainer = document.createElement('div');
                imgContainer.className = 'aspect-square bg-gray-100 rounded-lg border-2 border-gray-300 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative';
                
                // Create image element programmatically (not via innerHTML)
                const img = document.createElement('img');
                img.src = dataUrl;
                img.alt = `Preview ${index + 1}`;
                img.className = 'w-full h-full object-cover';
                img.style.display = 'block';
                img.onerror = function() {
                    console.error('Image failed to load:', file.name, 'DataURL length:', dataUrl ? dataUrl.length : 0);
                    img.style.display = 'none';
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'w-full h-full flex items-center justify-center text-red-500 text-sm bg-gray-100';
                    errorDiv.textContent = 'Failed to load';
                    imgContainer.appendChild(errorDiv);
                };
                img.onload = function() {
                    console.log('Image loaded successfully:', file.name, 'Dimensions:', this.width, 'x', this.height);
                };
                imgContainer.appendChild(img);
                
                // Add overlay (positioned absolutely, behind badge)
                const overlay = document.createElement('div');
                overlay.className = 'absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center pointer-events-none';
                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.4)';
                
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.onclick = function(e) { e.stopPropagation(); removeImageByKey(fileKey); };
                removeBtn.className = 'bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 pointer-events-auto transition duration-150 ease-in-out shadow-md';
                removeBtn.textContent = 'Remove';
                overlay.appendChild(removeBtn);
                imgContainer.appendChild(overlay);
                
                // Add number badge (positioned on top)
                const badge = document.createElement('span');
                badge.className = 'absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-full font-semibold shadow z-10';
                badge.textContent = index + 1;
                imgContainer.appendChild(badge);
                
                div.appendChild(imgContainer);
                if (preview) preview.appendChild(div);

                const altDiv = document.createElement('div');
                altDiv.className = 'flex items-start gap-3';
                altDiv.setAttribute('data-file-key', fileKey);
                altDiv.innerHTML = `
                    <label class="block text-sm font-medium text-gray-700 w-28 pt-2.5">Image ${index + 1}:</label>
                    <input type="text" name="image_alt_texts[]" value="${file.name.replace(/\.[^/.]+$/, '')}" 
                        class="flex-1 block w-full px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out text-sm"
                        placeholder="Alt text for image ${index + 1}">
                `;
                if (altTextsContainer) altTextsContainer.appendChild(altDiv);

                if (processedCount === 1 && previewContainer) {
                    previewContainer.style.display = 'block';
                }
            };
            reader.readAsDataURL(file);
        });

        // Update file count
        if (fileCount) fileCount.textContent = `${selectedFiles.length} image(s) selected`;
    }

    function updateFileInput() {
        // Create a new DataTransfer object
        const dataTransfer = new DataTransfer();
        
        // Add all selected files
        selectedFiles.forEach(file => {
            dataTransfer.items.add(file);
        });
        
        // Update the file input
        const input = document.getElementById('images');
        if (input) {
            input.files = dataTransfer.files;
        }
    }

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners with null checks
        const addInsurance = document.getElementById('add_insurance');
        if (addInsurance) {
            addInsurance.addEventListener('change', function() {
                const details = document.getElementById('insurance-details');
                if (details) details.style.display = this.checked ? 'block' : 'none';
            });
            // Show if already checked
            if (addInsurance.checked) {
                const details = document.getElementById('insurance-details');
                if (details) details.style.display = 'block';
            }
        }

        const addWarranty = document.getElementById('add_warranty');
        if (addWarranty) {
            addWarranty.addEventListener('change', function() {
                const details = document.getElementById('warranty-details');
                if (details) details.style.display = this.checked ? 'block' : 'none';
            });
            // Show if already checked
            if (addWarranty.checked) {
                const details = document.getElementById('warranty-details');
                if (details) details.style.display = 'block';
            }
        }

        const addMaintenanceTracking = document.getElementById('add_maintenance_tracking');
        if (addMaintenanceTracking) {
            addMaintenanceTracking.addEventListener('change', function() {
                const details = document.getElementById('maintenance-details');
                if (details) details.style.display = this.checked ? 'block' : 'none';
            });
            // Show if already checked
            if (addMaintenanceTracking.checked) {
                const details = document.getElementById('maintenance-details');
                if (details) details.style.display = 'block';
            }
        }

        const addLowStockAlert = document.getElementById('add_low_stock_alert');
        if (addLowStockAlert) {
            addLowStockAlert.addEventListener('change', function() {
                const details = document.getElementById('low-stock-details');
                if (details) details.style.display = this.checked ? 'block' : 'none';
            });
            // Show if already checked
            if (addLowStockAlert.checked) {
                const details = document.getElementById('low-stock-details');
                if (details) details.style.display = 'block';
            }
        }
    });
</script>
@endsection
