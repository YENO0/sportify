@extends('layouts.app')

@section('title', 'Sport Type Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('sport-types.index') }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Sport Types
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $sportType->name }}</h1>
        <p class="mt-2 text-sm text-gray-600">Sport Type Details</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Information</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sportType->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sportType->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            @if($sportType->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </dd>
                    </div>
                    @if($sportType->icon)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Icon</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sportType->icon }}</dd>
                        </div>
                    @endif
                    @if($sportType->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $sportType->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            @if($sportType->equipment->count() > 0)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Associated Equipment ({{ $sportType->equipment_count }})</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipment</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Brand</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($sportType->equipment->take(10) as $equipment)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('inventory.show', $equipment->id) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $equipment->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $equipment->brand->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $equipment->available_quantity }} / {{ $equipment->quantity }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColors = [
                                                    'available' => 'bg-green-100 text-green-800',
                                                    'maintenance' => 'bg-yellow-100 text-yellow-800',
                                                    'damaged' => 'bg-red-100 text-red-800',
                                                    'retired' => 'bg-gray-100 text-gray-800',
                                                ];
                                                $color = $statusColors[$equipment->status] ?? 'bg-gray-100 text-gray-800';
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                                {{ ucfirst($equipment->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($sportType->equipment_count > 10)
                        <p class="mt-4 text-sm text-gray-500">Showing 10 of {{ $sportType->equipment_count }} equipment items</p>
                    @endif
                </div>
            @else
                <div class="bg-white shadow rounded-lg p-6">
                    <p class="text-gray-500">No equipment associated with this sport type yet.</p>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('sport-types.edit', $sportType->id) }}" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 block text-center">
                        Edit Sport Type
                    </a>
                    <form action="{{ route('sport-types.destroy', $sportType->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sport type?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-700">
                            Delete Sport Type
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Equipment</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $sportType->equipment_count ?? 0 }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Created</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sportType->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $sportType->updated_at->format('M d, Y') }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

