@extends('layouts.app')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Inventory Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Manage your sports club equipment inventory</p>
        </div>
        <!-- Maintenance Dashboard Button -->
        <a href="{{ route('maintenance.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Maintenance Dashboard
        </a>
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

    <!-- Tabs Navigation -->
    <div class="bg-white shadow rounded-lg mb-6 border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-1 px-4" aria-label="Tabs">
                <button onclick="switchTab('equipment', false)" id="tab-equipment" class="tab-button {{ request('tab', 'equipment') === 'equipment' ? 'active border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'border-b-2 border-transparent text-gray-500' }} px-6 py-4 text-sm font-semibold rounded-t-lg transition-all duration-200">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        Equipment
                    </span>
                </button>
                <button onclick="switchTab('brands', false)" id="tab-brands" class="tab-button {{ request('tab') === 'brands' ? 'active border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'border-b-2 border-transparent text-gray-500' }} px-6 py-4 text-sm font-semibold rounded-t-lg transition-all duration-200 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Brands
                    </span>
                </button>
                <button onclick="switchTab('sport-types', false)" id="tab-sport-types" class="tab-button {{ request('tab') === 'sport-types' ? 'active border-b-2 border-blue-500 text-blue-600 bg-blue-50' : 'border-b-2 border-transparent text-gray-500' }} px-6 py-4 text-sm font-semibold rounded-t-lg transition-all duration-200 hover:text-gray-700 hover:bg-gray-50 hover:border-gray-300">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Sport Types
                    </span>
                </button>
            </nav>
        </div>
    </div>

    <!-- Equipment Tab Panel -->
    <div id="panel-equipment" class="tab-panel {{ request('tab', 'equipment') !== 'equipment' ? 'hidden' : '' }}">
    <!-- Equipment Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Equipment List</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">All equipment items in the inventory</p>
                </div>
                <a href="{{ route('inventory.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                    Add New Equipment
                </a>
            </div>
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
                            class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all bg-white">
                    </div>

                    <!-- Clear Button -->
                    @if(request('search') || request('status') || request('sport_type_id') || request('low_stock'))
                        <a href="{{ route('inventory.index', ['tab' => 'equipment']) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear
                        </a>
                    @endif
                </div>

                <!-- Filters Row -->
                <div class="flex flex-wrap items-center gap-4 pt-3">
                    <!-- Status Filter -->
                    <div class="flex items-center gap-2">
                        <label for="status" class="text-xs font-medium text-gray-600 whitespace-nowrap leading-none flex items-center h-7">Status:</label>
                        <select name="status" id="status" 
                            class="block w-36 px-3 py-1 h-7 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">All Statuses</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="damaged" {{ request('status') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                            <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                    </div>

                    <!-- Sport Type Filter -->
                    <div class="flex items-center gap-2">
                        <label for="sport_type_id" class="text-xs font-medium text-gray-600 whitespace-nowrap leading-none flex items-center h-7">Sport Type:</label>
                        <select name="sport_type_id" id="sport_type_id" 
                            class="block w-40 px-3 py-1 h-7 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">All Sport Types</option>
                            @foreach($sportTypes as $sportType)
                                <option value="{{ $sportType->id }}" {{ request('sport_type_id') == $sportType->id ? 'selected' : '' }}>
                                    {{ $sportType->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Low Stock Filter -->
                    <div class="flex items-center gap-2">
                        <label for="low_stock" class="flex items-center cursor-pointer group h-7">
                            <input type="checkbox" name="low_stock" id="low_stock" value="1" {{ request('low_stock') == '1' ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-all">
                            <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900 leading-none">Low Stock Only</span>
                        </label>
                    </div>
                </div>

                <!-- Preserve sort parameters and tab -->
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
                <!-- Always preserve tab parameter for equipment tab -->
                <input type="hidden" name="tab" value="{{ request('tab', 'equipment') }}">
            </form>
        </div>
        
        <div class="overflow-x-auto min-h-[600px]" id="inventory-table-container">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'name', 'label' => 'Name'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'sport_type_id', 'label' => 'Sport Type'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                            Brand / Model
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'quantity', 'label' => 'Quantity'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'available_quantity', 'label' => 'Available'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'status', 'label' => 'Status'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                            @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'location', 'label' => 'Location'])
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-black uppercase tracking-wider">
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

    <!-- Brands Tab Panel -->
    <div id="panel-brands" class="tab-panel {{ request('tab') !== 'brands' ? 'hidden' : '' }}">
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Registered Brands</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">All brands available for equipment registration</p>
                    </div>
                    <a href="{{ route('brands.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                        Register New Brand
                    </a>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white">
                <form method="GET" action="{{ route('inventory.index') }}" id="brands-search-form" class="space-y-3">
                    <input type="hidden" name="tab" value="brands">
                    <!-- Main Search Row -->
                    <div class="flex items-center gap-3">
                        <!-- Search Input with Icon -->
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="brand_search" id="brands-search" value="{{ request('brand_search') }}" 
                                placeholder="Search brands by name or description..."
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all bg-white">
                        </div>

                        <!-- Clear Button -->
                        @if(request('brand_search') || request('brand_sort'))
                            <a href="{{ route('inventory.index', ['tab' => 'brands']) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear
                            </a>
                        @endif
                    </div>
                    
                    @if(request('brand_sort'))
                        <input type="hidden" name="brand_sort" value="{{ request('brand_sort') }}">
                    @endif
                    @if(request('brand_direction'))
                        <input type="hidden" name="brand_direction" value="{{ request('brand_direction') }}">
                    @endif
                    <!-- Always preserve tab parameter for brands tab -->
                    <input type="hidden" name="tab" value="brands">
                </form>
            </div>
            
            <div class="overflow-x-auto" id="brands-table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                                @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'name', 'label' => 'Brand Name', 'param_prefix' => 'brand_'])
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                                @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'equipment_count', 'label' => 'Equipment Count', 'param_prefix' => 'brand_'])
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                                Website
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-black uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($brands as $brand)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $brand->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500">{{ Str::limit($brand->description ?? 'N/A', 50) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $brand->equipment_count }} items
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($brand->website)
                                        <a href="{{ $brand->website }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                            Visit Website
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('brands.show', $brand->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('brands.edit', $brand->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <form action="{{ route('brands.destroy', $brand->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center">
                                    @if(request('brand_search'))
                                        <div class="text-sm text-gray-500">
                                            <p class="mb-2">No brands found matching your search.</p>
                                            <p class="text-xs text-gray-400">Try different search terms.</p>
                                        </div>
                                    @else
                                        <div class="text-sm text-gray-500">
                                            No brands registered yet. <a href="{{ route('brands.create') }}" class="text-blue-600 hover:text-blue-900">Register your first brand</a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($brands->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $brands->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Sport Types Tab Panel -->
    <div id="panel-sport-types" class="tab-panel {{ request('tab') !== 'sport-types' ? 'hidden' : '' }}">
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">All Sport Types</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">List of all sport types used for equipment categorization</p>
                    </div>
                    <a href="{{ route('sport-types.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                        + Add Sport Type
                    </a>
                </div>
            </div>

            <!-- Search and Filter Bar -->
            <div class="px-6 py-4 border-b border-gray-200 bg-white">
                <form method="GET" action="{{ route('inventory.index') }}" id="sport-types-search-form" class="space-y-3">
                    <input type="hidden" name="tab" value="sport-types">
                    <!-- Main Search Row -->
                    <div class="flex items-center gap-3">
                        <!-- Search Input with Icon -->
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" name="sport_type_search" id="sport-types-search" value="{{ request('sport_type_search') }}" 
                                placeholder="Search sport types by name, description, or slug..."
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base transition-all bg-white">
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center gap-2">
                            <label for="sport_type_is_active" class="text-xs font-medium text-gray-600 whitespace-nowrap leading-none flex items-center h-11">Status:</label>
                            <select name="sport_type_is_active" id="sport_type_is_active" 
                                class="block w-40 px-3 py-3 h-11 border border-gray-300 rounded-lg shadow-sm text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                                <option value="">All Statuses</option>
                                <option value="1" {{ request('sport_type_is_active') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ request('sport_type_is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Clear Button -->
                        @if(request('sport_type_search') || request('sport_type_is_active') || request('sport_type_sort'))
                            <a href="{{ route('inventory.index', ['tab' => 'sport-types']) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Clear
                            </a>
                        @endif
                    </div>
                    
                    @if(request('sport_type_sort'))
                        <input type="hidden" name="sport_type_sort" value="{{ request('sport_type_sort') }}">
                    @endif
                    @if(request('sport_type_direction'))
                        <input type="hidden" name="sport_type_direction" value="{{ request('sport_type_direction') }}">
                    @endif
                    <!-- Always preserve tab parameter for sport-types tab -->
                    <input type="hidden" name="tab" value="sport-types">
                </form>
            </div>
            
            <div id="sport-types-content-container">
            @if($sportTypesList->count() > 0)
                <div class="overflow-x-auto" id="sport-types-table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                                    @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'name', 'label' => 'Name', 'param_prefix' => 'sport_type_'])
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">
                                    @include('partials.sortable-header', ['route' => 'inventory.index', 'column' => 'equipment_count', 'label' => 'Equipment Count', 'param_prefix' => 'sport_type_'])
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-black uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sportTypesList as $sportType)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $sportType->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $sportType->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ Str::limit($sportType->description ?? 'No description', 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $sportType->equipment_count ?? 0 }} equipment
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($sportType->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('sport-types.show', $sportType->id) }}" class="text-blue-600 hover:text-blue-900 mr-4">View</a>
                                        <a href="{{ route('sport-types.edit', $sportType->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</a>
                                        <form action="{{ route('sport-types.destroy', $sportType->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this sport type?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($sportTypesList->hasPages())
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $sportTypesList->links() }}
                    </div>
                @endif
            @else
                <div class="px-4 py-5 text-center">
                    @if(request('sport_type_search') || request('sport_type_is_active'))
                        <div class="text-sm text-gray-500">
                            <p class="mb-2">No sport types found matching your search criteria.</p>
                            <p class="text-xs text-gray-400">Try adjusting your filters or search terms.</p>
                        </div>
                    @else
                        <p class="text-gray-500">No sport types found. <a href="{{ route('sport-types.create') }}" class="text-blue-600 hover:text-blue-900">Create one now</a></p>
                    @endif
                </div>
            @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Tab switching functionality
    function switchTab(tabName, preserveParams = false) {
        // Check if tab is already active
        const currentTab = document.querySelector('.tab-button.active');
        const currentPanel = document.querySelector('.tab-panel:not(.hidden)');
        const isAlreadyActive = currentTab && currentTab.id === 'tab-' + tabName;
        
        // Only hide/show if switching to a different tab
        if (!isAlreadyActive) {
            // Hide all panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.add('hidden');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-blue-500', 'text-blue-600', 'bg-blue-50');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected panel
            const panel = document.getElementById('panel-' + tabName);
            if (panel) {
                panel.classList.remove('hidden');
            }
            
            // Activate selected tab
            const tab = document.getElementById('tab-' + tabName);
            if (tab) {
                tab.classList.add('active', 'border-blue-500', 'text-blue-600', 'bg-blue-50');
                tab.classList.remove('border-transparent', 'text-gray-500');
            }
        }
        
        // Only update URL if not preserving params (i.e., when manually switching tabs)
        if (!preserveParams) {
            // Update URL without reload
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            // Clear other tab's search params when switching tabs
            if (tabName === 'equipment') {
                url.searchParams.delete('brand_search');
                url.searchParams.delete('brand_sort');
                url.searchParams.delete('brand_direction');
                url.searchParams.delete('sport_type_search');
                url.searchParams.delete('sport_type_is_active');
                url.searchParams.delete('sport_type_sort');
                url.searchParams.delete('sport_type_direction');
            } else if (tabName === 'brands') {
                url.searchParams.delete('search');
                url.searchParams.delete('status');
                url.searchParams.delete('sport_type_id');
                url.searchParams.delete('low_stock');
                url.searchParams.delete('sort');
                url.searchParams.delete('direction');
                url.searchParams.delete('sport_type_search');
                url.searchParams.delete('sport_type_is_active');
                url.searchParams.delete('sport_type_sort');
                url.searchParams.delete('sport_type_direction');
            } else if (tabName === 'sport-types') {
                url.searchParams.delete('search');
                url.searchParams.delete('status');
                url.searchParams.delete('sport_type_id');
                url.searchParams.delete('low_stock');
                url.searchParams.delete('sort');
                url.searchParams.delete('direction');
                url.searchParams.delete('brand_search');
                url.searchParams.delete('brand_sort');
                url.searchParams.delete('brand_direction');
            }
            window.history.pushState({}, '', url);
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Since we're setting initial state server-side, minimize JavaScript manipulation
        // Only verify and fix if there's a mismatch (shouldn't happen, but safety check)
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'equipment';
        const expectedTabId = 'tab-' + activeTab;
        const currentActiveTab = document.querySelector('.tab-button.active');
        const expectedPanel = document.getElementById('panel-' + activeTab);
        
        // Only fix if there's actually a mismatch (should be rare with server-side rendering)
        // Skip all DOM manipulation if everything is already correct to prevent double-load effect
        if (!currentActiveTab || currentActiveTab.id !== expectedTabId || !expectedPanel || expectedPanel.classList.contains('hidden')) {
            // Only call switchTab if there's a mismatch (preserve params)
            switchTab(activeTab, true);
        }
        // If everything is correct, do absolutely nothing - this prevents the double-load effect
        
        const form = document.getElementById('inventory-search-form');
        const statusSelect = document.getElementById('status');
        const sportTypeSelect = document.getElementById('sport_type_id');
        const lowStockCheckbox = document.getElementById('low_stock');
        
        // Store scroll position before form submission
        let scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        
        // Restore scroll position after page load
        if (sessionStorage.getItem('inventoryScrollPosition')) {
            const savedPosition = parseInt(sessionStorage.getItem('inventoryScrollPosition'));
            window.scrollTo(0, savedPosition);
            sessionStorage.removeItem('inventoryScrollPosition');
        }
        
        // Function to submit form while preserving scroll position
        function submitFormPreserveScroll() {
            scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
            sessionStorage.setItem('inventoryScrollPosition', scrollPosition.toString());
            if (form) form.submit();
        }
        
        // Auto-submit on filter change
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                submitFormPreserveScroll();
            });
        }
        
        if (sportTypeSelect) {
            sportTypeSelect.addEventListener('change', function() {
                submitFormPreserveScroll();
            });
        }
        
        if (lowStockCheckbox) {
            lowStockCheckbox.addEventListener('change', function() {
                submitFormPreserveScroll();
            });
        }
        
        // Auto-submit search on input (with debounce)
        const searchInput = document.getElementById('inventory-search');
        let searchTimeout;
        let isEquipmentSubmitting = false;
        
        if (searchInput && form) {
            // Auto-submit after user stops typing (500ms delay)
            searchInput.addEventListener('input', function() {
                if (isEquipmentSubmitting) return;
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    if (isEquipmentSubmitting) return;
                    isEquipmentSubmitting = true;
                    submitFormPreserveScroll();
                }, 500); // Wait 500ms after user stops typing
            });
            
            // Also submit on Enter key for immediate search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    if (isEquipmentSubmitting) {
                        e.preventDefault();
                        return;
                    }
                    clearTimeout(searchTimeout);
                    e.preventDefault();
                    isEquipmentSubmitting = true;
                    submitFormPreserveScroll();
                }
            });
            
            // Reset submitting flag after form submission
            form.addEventListener('submit', function() {
                isEquipmentSubmitting = true;
            });
        }
        
        // Prevent form from scrolling to top on submit
        if (form) {
            form.addEventListener('submit', function(e) {
                scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
                sessionStorage.setItem('inventoryScrollPosition', scrollPosition.toString());
            });
        }
        
        // Handle brands search form
        const brandsForm = document.getElementById('brands-search-form');
        const brandsSearchInput = document.getElementById('brands-search');
        if (brandsSearchInput && brandsForm) {
            let brandsSearchTimeout;
            let isBrandsSubmitting = false;
            
            brandsSearchInput.addEventListener('input', function() {
                if (isBrandsSubmitting) return; // Prevent double submission
                clearTimeout(brandsSearchTimeout);
                brandsSearchTimeout = setTimeout(function() {
                    if (isBrandsSubmitting) return;
                    isBrandsSubmitting = true;
                    // Ensure tab parameter is set
                    const tabInput = brandsForm.querySelector('input[name="tab"]');
                    if (!tabInput) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'tab';
                        hiddenInput.value = 'brands';
                        brandsForm.appendChild(hiddenInput);
                    }
                    brandsForm.submit();
                }, 500);
            });
            
            // Also submit on Enter key
            brandsSearchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    if (isBrandsSubmitting) {
                        e.preventDefault();
                        return;
                    }
                    clearTimeout(brandsSearchTimeout);
                    e.preventDefault();
                    isBrandsSubmitting = true;
                    const tabInput = brandsForm.querySelector('input[name="tab"]');
                    if (!tabInput) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'tab';
                        hiddenInput.value = 'brands';
                        brandsForm.appendChild(hiddenInput);
                    }
                    brandsForm.submit();
                }
            });
        }
        
        // Handle sport types search form
        const sportTypesForm = document.getElementById('sport-types-search-form');
        const sportTypesSearchInput = document.getElementById('sport-types-search');
        const sportTypesStatusSelect = document.getElementById('sport_type_is_active');
        
        if (sportTypesSearchInput && sportTypesForm) {
            let sportTypesSearchTimeout;
            let isSubmitting = false;
            
            sportTypesSearchInput.addEventListener('input', function() {
                if (isSubmitting) return; // Prevent double submission
                clearTimeout(sportTypesSearchTimeout);
                sportTypesSearchTimeout = setTimeout(function() {
                    if (isSubmitting) return;
                    isSubmitting = true;
                    // Ensure tab parameter is set
                    const tabInput = sportTypesForm.querySelector('input[name="tab"]');
                    if (!tabInput) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'tab';
                        hiddenInput.value = 'sport-types';
                        sportTypesForm.appendChild(hiddenInput);
                    }
                    sportTypesForm.submit();
                }, 500);
            });
            
            // Also submit on Enter key
            sportTypesSearchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    if (isSubmitting) {
                        e.preventDefault();
                        return;
                    }
                    clearTimeout(sportTypesSearchTimeout);
                    e.preventDefault();
                    isSubmitting = true;
                    const tabInput = sportTypesForm.querySelector('input[name="tab"]');
                    if (!tabInput) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'tab';
                        hiddenInput.value = 'sport-types';
                        sportTypesForm.appendChild(hiddenInput);
                    }
                    sportTypesForm.submit();
                }
            });
            
            // Reset submitting flag after form submission
            sportTypesForm.addEventListener('submit', function() {
                isSubmitting = true;
            });
        }
        
        if (sportTypesStatusSelect && sportTypesForm) {
            let isStatusSubmitting = false;
            sportTypesStatusSelect.addEventListener('change', function() {
                if (isStatusSubmitting) return;
                isStatusSubmitting = true;
                // Ensure tab parameter is set
                const tabInput = sportTypesForm.querySelector('input[name="tab"]');
                if (!tabInput) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'tab';
                    hiddenInput.value = 'sport-types';
                    sportTypesForm.appendChild(hiddenInput);
                }
                sportTypesForm.submit();
            });
        }
    });
</script>
@endpush
@endsection

