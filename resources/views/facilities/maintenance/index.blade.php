@extends('layouts.app')

@section('title', 'Facility Maintenance Dashboard')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Facility Maintenance Dashboard</h1>

    <div class="flex justify-end mb-4">
        <a href="{{ route('facilities.maintenance.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Schedule New Maintenance
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-max w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Facility</th>
                    <th class="py-3 px-6 text-left">Title</th>
                    <th class="py-3 px-6 text-left">Description</th>
                    <th class="py-3 px-6 text-left">Start Date</th>
                    <th class="py-3 px-6 text-left">End Date</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse($maintenances as $maintenance)
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left whitespace-nowrap">
                            {{ $maintenance->facility->name }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $maintenance->title }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ Str::limit($maintenance->description, 50) }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $maintenance->start_date->format('M d, Y H:i') }}
                        </td>
                        <td class="py-3 px-6 text-left">
                            {{ $maintenance->end_date->format('M d, Y H:i') }}
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center">
                                <a href="{{ route('facilities.maintenance.edit', $maintenance->id) }}" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L15.232 5.232z" />
                                    </svg>
                                </a>
                                <form action="{{ route('facilities.maintenance.destroy', $maintenance->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this maintenance schedule?');" class="w-4 mr-2 transform hover:text-purple-500 hover:scale-110">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-4 h-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-3 px-6 text-center">No maintenance schedules found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
