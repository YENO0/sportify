@extends('layouts.app')

@section('title', 'Sport Types Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sport Types Management</h1>
            <p class="mt-2 text-sm text-gray-600">Manage sport types for equipment categorization</p>
        </div>
        <a href="{{ route('sport-types.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
            + Add Sport Type
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">All Sport Types</h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">List of all sport types used for equipment categorization</p>
        </div>

        <!-- Search and Filter Bar -->
        <div class="px-4 py-4 border-b border-gray-200 bg-gray-50">
            <form method="GET" action="{{ route('sport-types.index') }}" id="sport-types-search-form" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label for="sport-types-search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="sport-types-search" value="{{ request('search') }}" 
                        placeholder="Search by name, description, or slug..."
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="min-w-[150px]">
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="is_active" id="is_active" 
                        class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                @if(request('direction'))
                    <input type="hidden" name="direction" value="{{ request('direction') }}">
                @endif
                <div class="flex gap-2">
                    <a href="{{ route('sport-types.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                        Clear All
                    </a>
                </div>
            </form>
        </div>
        
        <div id="sport-types-content-container">
        @if($sportTypes->count() > 0)
            <div class="overflow-x-auto" id="sport-types-table-container">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                @include('partials.sortable-header', ['route' => 'sport-types.index', 'column' => 'name', 'label' => 'Name'])
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                @include('partials.sortable-header', ['route' => 'sport-types.index', 'column' => 'equipment_count', 'label' => 'Equipment Count'])
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($sportTypes as $sportType)
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
            
            @if($sportTypes->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $sportTypes->links() }}
                </div>
            @endif
        @else
            <div class="px-4 py-5 text-center">
                @if(request('search') || request('is_active'))
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
@endsection

