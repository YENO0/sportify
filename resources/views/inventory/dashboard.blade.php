@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Inventory Dashboard</h1>
        <p class="mt-2 text-sm text-gray-600">Manage your sports club equipment inventory</p>
    </div>

    <!-- Low Stock Alert Banner -->
    @if($lowStockEquipment->count() > 0)
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-medium text-red-800">
                        ⚠️ Low Stock Alert: {{ $lowStockEquipment->count() }} equipment item(s) below minimum stock level
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($lowStockEquipment->take(5) as $item)
                                <li>
                                    <a href="{{ route('inventory.show', $item->id) }}" class="font-medium hover:underline">
                                        {{ $item->name }}
                                    </a>
                                    - Available: {{ $item->available_quantity }}, Minimum: {{ $item->minimum_stock_amount }}
                                    (Shortage: {{ $item->minimum_stock_amount - $item->available_quantity }})
                                </li>
                            @endforeach
                            @if($lowStockEquipment->count() > 5)
                                <li class="font-medium">... and {{ $lowStockEquipment->count() - 5 }} more</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Equipment</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_equipment'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Available</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['available_equipment'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">In Maintenance</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['maintenance_equipment'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Value</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($stats['total_value'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 {{ $stats['low_stock_count'] > 0 ? 'bg-red-500' : 'bg-orange-500' }} rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Items</dt>
                            <dd class="text-lg font-medium {{ $stats['low_stock_count'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $stats['low_stock_count'] }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Equipment List</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">All equipment items in the inventory</p>
        </div>

        <!-- Search and Filter Bar -->
        <div class="px-6 py-4 border-b border-gray-200 bg-white">
            <form method="GET" action="{{ route('inventory.index') }}" id="inventory-search-form" class="space-y-3">
                <!-- Main Search Row -->
                <div class="flex items-center gap-3">
                    <!-- Search Input with Icon -->
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" id="inventory-search" value="{{ request('search') }}" 
                            placeholder="Search equipment by name, model, brand, or sport type..."
                            class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all">
                    </div>

                    <!-- Search Button -->
                    <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Search
                    </button>

                    <!-- Clear Button -->
                    @if(request('search') || request('status') || request('sport_type_id') || request('low_stock'))
                        <a href="{{ route('inventory.index') }}" class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear
                        </a>
                    @endif
                </div>

                <!-- Filters Row -->
                <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-gray-100">
                    <!-- Status Filter -->
                    <div class="flex items-center gap-2">
                        <label for="status" class="text-xs font-medium text-gray-600 whitespace-nowrap">Status:</label>
                        <select name="status" id="status" 
                            class="block w-36 px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">All Statuses</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                    </div>

                    <!-- Sport Type Filter -->
                    <div class="flex items-center gap-2">
                        <label for="sport_type_id" class="text-xs font-medium text-gray-600 whitespace-nowrap">Sport Type:</label>
                        <select name="sport_type_id" id="sport_type_id" 
                            class="block w-40 px-3 py-2 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">All Sport Types</option>
                            @foreach($sportTypes as $sportType)
                                <option value="{{ $sportType->id }}" {{ request('sport_type_id') == $sportType->id ? 'selected' : '' }}>
                                    {{ $sportType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Low Stock Filter -->
                    <div class="flex items-center gap-2 ml-auto">
                        <label for="low_stock" class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="low_stock" id="low_stock" value="1" {{ request('low_stock') == '1' ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all">
                            <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900">Low Stock Only</span>
                        </label>
                    </div>
                </div>

                <!-- Preserve sort parameters -->
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
            </form>
        </div>
        
        <div class="overflow-x-auto" id="inventory-table-container">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'name', 'label' => 'Name'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'sport_type_id', 'label' => 'Sport Type'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Brand / Model
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'quantity', 'label' => 'Quantity'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'available_quantity', 'label' => 'Available'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'status', 'label' => 'Status'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'location', 'label' => 'Location'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($equipment as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
                                    @if($item->isLowStock())
                                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800" title="Low Stock">
                                            ⚠️
                                        </span>
                                    @endif
                                </div>
                                @if($item->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->sportType)
                                    <a href="{{ route('sport-types.show', $item->sportType->id) }}" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 hover:bg-blue-200">
                                        {{ $item->sportType->name }}
                                    </a>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Not Set
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($item->brand)
                                    <div class="font-medium">{{ $item->brand->name }}</div>
                                    @if($item->model)
                                        <div class="text-xs text-gray-400">{{ $item->model }}</div>
                                    @endif
                                @else
                                    <span class="text-gray-400">No brand</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->quantity }}
                                @if($item->minimum_stock_amount > 0)
                                    <span class="text-xs text-gray-400">(Min: {{ $item->minimum_stock_amount }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $item->isLowStock() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                    {{ $item->available_quantity }}
                                </div>
                                @if($item->isLowStock())
                                    <div class="text-xs text-red-500">
                                        Below minimum!
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'available' => 'bg-green-100 text-green-800',
                                        'maintenance' => 'bg-yellow-100 text-yellow-800',
                                        'damaged' => 'bg-red-100 text-red-800',
                                        'retired' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $color = $statusColors[$item->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->location ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('inventory.show', $item->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                <a href="{{ route('inventory.edit', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                <form action="{{ route('inventory.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this equipment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center">
                                @if(request('search') || request('status') || request('sport_type_id') || request('low_stock'))
                                    <div class="text-sm text-gray-500">
                                        <p class="mb-2">No equipment found matching your search criteria.</p>
                                        <p class="text-xs text-gray-400">Try adjusting your filters or search terms.</p>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">
                                        No equipment found. <a href="{{ route('inventory.create') }}" class="text-blue-600 hover:text-blue-900">Add your first equipment item</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($equipment->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $equipment->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('inventory-search-form');
        const statusSelect = document.getElementById('status');
        const sportTypeSelect = document.getElementById('sport_type_id');
        const lowStockCheckbox = document.getElementById('low_stock');
        
        // Auto-submit on filter change
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                form.submit();
            });
        }
        
        if (sportTypeSelect) {
            sportTypeSelect.addEventListener('change', function() {
                form.submit();
            });
        }
        
        if (lowStockCheckbox) {
            lowStockCheckbox.addEventListener('change', function() {
                form.submit();
            });
        }
        
        // Search input: submit on Enter key
        const searchInput = document.getElementById('inventory-search');
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        }
    });
</script>
@endpush
@endsection

