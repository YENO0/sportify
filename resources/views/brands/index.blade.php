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
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Brand Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Equipment Count
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
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No brands registered yet. <a href="{{ route('brands.create') }}" class="text-blue-600 hover:text-blue-900">Register your first brand</a>
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

