@extends('layouts.app')

@section('title', 'Maintenance Dashboard')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Maintenance Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Manage equipment maintenance schedules and track maintenance activities</p>
        </div>
        <a href="{{ route('maintenance.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
            Schedule Maintenance
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-5 mb-8">
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['total_pending'] }}</dd>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Overdue</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['overdue'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">In Progress</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['in_progress'] }}</dd>
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
                            <dt class="text-sm font-medium text-gray-500 truncate">Completed (Month)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $stats['completed_this_month'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Cost (Month)</dt>
                            <dd class="text-lg font-medium text-gray-900">${{ number_format($stats['total_cost'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md mb-8">
        <div class="px-4 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ route('maintenance.index') }}" id="maintenance-search-form" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="maintenance-search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="maintenance-search" value="{{ request('search') }}" 
                        placeholder="Search by equipment name, description, or notes..."
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="min-w-[150px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="min-w-[200px]">
                    <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-1">Equipment</label>
                    <select name="equipment_id" id="equipment_id" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Equipment</option>
                        @foreach($equipmentList as $equipment)
                            <option value="{{ $equipment->id }}" {{ request('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                {{ $equipment->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
                <div class="flex gap-2">
                    <a href="{{ route('maintenance.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                        Clear All
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Overdue Maintenances -->
    @if($overdueMaintenances->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md mb-8">
            <div class="px-4 py-5 sm:px-6 border-b border-red-200 bg-red-50">
                <h3 class="text-lg leading-6 font-medium text-red-900">⚠️ Overdue Maintenances</h3>
                <p class="mt-1 max-w-2xl text-sm text-red-700">Maintenances that are past their scheduled date</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @include('partials.sortable-header', ['route' => 'maintenance.index', 'column' => 'start_date', 'label' => 'Start Date'])
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @include('partials.sortable-header', ['route' => 'maintenance.index', 'column' => 'end_date', 'label' => 'End Date'])
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Overdue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($overdueMaintenances as $maintenance)
                            <tr class="bg-red-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('inventory.show', $maintenance->equipment_id) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $maintenance->equipment->name }}
                                        </a>
                                    </div>
                                    @if($maintenance->equipment->brand)
                                        <div class="text-sm text-gray-500">{{ $maintenance->equipment->brand->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ ucfirst($maintenance->maintenance_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $maintenance->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $maintenance->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($maintenance->start_date ?? $maintenance->scheduled_date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $maintenance->end_date ? $maintenance->end_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $checkDate = $maintenance->start_date ?? $maintenance->scheduled_date;
                                        $daysOverdue = $checkDate->diffInDays(now());
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        {{ $daysOverdue }} days
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $maintenance->assignedUser->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="updateStatus({{ $maintenance->id }}, 'in_progress')" class="text-blue-600 hover:text-blue-900 mr-3">Start</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- In Progress Maintenances -->
    @if($inProgressMaintenances->count() > 0)
        <div class="bg-white shadow overflow-hidden sm:rounded-md mb-8">
            <div class="px-4 py-5 sm:px-6 border-b border-blue-200 bg-blue-50">
                <h3 class="text-lg leading-6 font-medium text-blue-900">🔧 In Progress Maintenances</h3>
                <p class="mt-1 max-w-2xl text-sm text-blue-700">Maintenances currently being worked on</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @include('partials.sortable-header', ['route' => 'maintenance.index', 'column' => 'start_date', 'label' => 'Start Date'])
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @include('partials.sortable-header', ['route' => 'maintenance.index', 'column' => 'end_date', 'label' => 'End Date'])
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($inProgressMaintenances as $maintenance)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('inventory.show', $maintenance->equipment_id) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $maintenance->equipment->name }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ ucfirst($maintenance->maintenance_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $maintenance->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $maintenance->quantity }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($maintenance->start_date ?? $maintenance->scheduled_date)->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $maintenance->end_date ? $maintenance->end_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $maintenance->assignedUser->name ?? 'Unassigned' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="updateStatus({{ $maintenance->id }}, 'completed')" class="text-green-600 hover:text-green-900">Complete</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Upcoming Maintenances -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">📅 Upcoming Maintenances (Next 30 Days)</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">Scheduled maintenances for the next month</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @include('partials.sortable-header', ['route' => 'maintenance.index', 'column' => 'start_date', 'label' => 'Start Date'])
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                @include('partials.sortable-header', ['route' => 'maintenance.index', 'column' => 'end_date', 'label' => 'End Date'])
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Until</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($upcomingMaintenances as $maintenance)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('inventory.show', $maintenance->equipment_id) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $maintenance->equipment->name }}
                                    </a>
                                </div>
                                @if($maintenance->equipment->brand)
                                    <div class="text-sm text-gray-500">{{ $maintenance->equipment->brand->name }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ ucfirst($maintenance->maintenance_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $maintenance->title }}</div>
                                @if($maintenance->description)
                                    <div class="text-sm text-gray-500">{{ Str::limit($maintenance->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $maintenance->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ ($maintenance->start_date ?? $maintenance->scheduled_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $maintenance->end_date ? $maintenance->end_date->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $checkDate = $maintenance->start_date ?? $maintenance->scheduled_date;
                                    $daysUntil = $checkDate->diffInDays(now());
                                    $color = $daysUntil <= 7 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                    {{ $daysUntil }} days
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $maintenance->assignedUser->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button onclick="updateStatus({{ $maintenance->id }}, 'in_progress')" class="text-blue-600 hover:text-blue-900 mr-3">Start</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center">
                                @if(request('search') || request('status') || request('equipment_id'))
                                    <div class="text-sm text-gray-500">
                                        <p class="mb-2">No upcoming maintenances found matching your search criteria.</p>
                                        <p class="text-xs text-gray-400">Try adjusting your filters or search terms.</p>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">
                                        No upcoming maintenances scheduled.
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($upcomingMaintenances->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $upcomingMaintenances->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold text-gray-900 mb-4" id="modalTitle">Update Maintenance Status</h3>
        <form id="statusForm" method="POST">
            @csrf
            <input type="hidden" name="status" id="statusInput">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Technician Notes</label>
                    <textarea name="technician_notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
                <div id="costField" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700">Cost</label>
                    <input type="number" name="cost" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateStatus(maintenanceId, status) {
        document.getElementById('statusForm').action = `/maintenance/${maintenanceId}/status`;
        document.getElementById('statusInput').value = status;
        document.getElementById('modalTitle').textContent = `Update Status to ${status.charAt(0).toUpperCase() + status.slice(1).replace('_', ' ')}`;
        
        if (status === 'completed') {
            document.getElementById('costField').style.display = 'block';
        } else {
            document.getElementById('costField').style.display = 'none';
        }
        
        document.getElementById('statusModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }
</script>
@endsection

