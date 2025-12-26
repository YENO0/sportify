@extends('layouts.app')

@section('title', 'Brand Details')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <a href="{{ route('inventory.index', ['tab' => 'brands']) }}" class="text-blue-600 hover:text-blue-900 mb-4 inline-block">
            ← Back to Dashboard
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ $brand->name }}</h1>
        <p class="mt-2 text-sm text-gray-600">Brand Details</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Brand Information</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Brand Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $brand->name }}</dd>
                    </div>
                    @if($brand->website)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Website</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <a href="{{ $brand->website }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                    {{ $brand->website }}
                                </a>
                            </dd>
                        </div>
                    @endif
                    @if($brand->contact_email)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $brand->contact_email }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Equipment Count</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $brand->equipment_count }} items
                            </span>
                        </dd>
                    </div>
                </dl>
                @if($brand->description)
                    <div class="mt-6">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $brand->description }}</dd>
                    </div>
                @endif
            </div>

            @if($brand->equipment->count() > 0)
                <div class="bg-white shadow-lg rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Equipment Items</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">Model</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold text-black uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($brand->equipment as $equipment)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $equipment->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if($equipment->sportType)
                                                {{ $equipment->sportType->name }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ $equipment->model ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
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
                                        <td class="px-6 py-4 text-sm font-medium">
                                            <a href="{{ route('inventory.show', $equipment->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                <div class="space-y-3">
                    <a href="{{ route('brands.edit', $brand->id) }}" class="w-full inline-flex items-center justify-center bg-blue-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-blue-700 transition duration-150 ease-in-out">
                        Edit Brand
                    </a>
                    <a href="{{ route('inventory.create') }}?brand_id={{ $brand->id }}" class="w-full inline-flex items-center justify-center bg-green-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-green-700 transition duration-150 ease-in-out">
                        Add Equipment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

