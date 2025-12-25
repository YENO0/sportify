@extends('layouts.app')

@section('title', 'Brand Registration')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Brand Registration</h1>
            <p class="mt-2 text-sm text-gray-600">Manage equipment brands</p>
        </div>
        <a href="{{ route('brands.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
            Register New Brand
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Registered Brands</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">All brands available for equipment registration</p>
        </div>
<<<<<<< HEAD

        <!-- Search Bar -->
        <div class="px-4 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ route('brands.index') }}" id="brands-search-form" class="flex gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="brands-search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="brands-search" value="{{ request('search') }}" 
                        placeholder="Search by name or description..."
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
                <div class="flex gap-2">
                    <a href="{{ route('brands.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                        Clear
                    </a>
                </div>
            </form>
        </div>
        
        <div class="overflow-x-auto" id="brands-table-container">
=======
        
        <div class="overflow-x-auto">
>>>>>>> origin/eewen
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
<<<<<<< HEAD
                            @include('partials.sortable-header', ['route' => 'brands.index', 'column' => 'name', 'label' => 'Brand Name'])
=======
                            Brand Name
>>>>>>> origin/eewen
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
<<<<<<< HEAD
                            @include('partials.sortable-header', ['route' => 'brands.index', 'column' => 'equipment_count', 'label' => 'Equipment Count'])
=======
                            Equipment Count
>>>>>>> origin/eewen
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Website
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
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
<<<<<<< HEAD
                            <td colspan="5" class="px-6 py-4 text-center">
                                @if(request('search'))
                                    <div class="text-sm text-gray-500">
                                        <p class="mb-2">No brands found matching your search.</p>
                                        <p class="text-xs text-gray-400">Try different search terms.</p>
                                    </div>
                                @else
                                    <div class="text-sm text-gray-500">
                                        No brands registered yet. <a href="{{ route('brands.create') }}" class="text-blue-600 hover:text-blue-900">Register your first brand</a>
                                    </div>
                                @endif
=======
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No brands registered yet. <a href="{{ route('brands.create') }}" class="text-blue-600 hover:text-blue-900">Register your first brand</a>
>>>>>>> origin/eewen
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
@endsection

