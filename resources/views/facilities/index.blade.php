@extends('layouts.app')

@section('title', 'Facility Dashboard')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Facility Dashboard</h1>

    <div class="flex justify-end mb-4 gap-2">
        <a href="{{ route('facilities.timetable') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700" style="background-color: #4f46e5; color: white;">Timetable</a>
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('facilities.maintenance.index') }}" class="bg-yellow-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-yellow-700">Schedule Maintenance</a>
            @endif
        @endauth
        <a href="{{ route('facilities.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">Add Facility</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($facilities as $facility)
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                @if($facility->image)
                    <img src="{{ route('facilities.photo', ['filename' => basename($facility->image)]) }}" alt="{{ $facility->name }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500">No Image</span>
                    </div>
                @endif
                <div class="p-4">
                    <h3 class="text-lg font-bold">
                        <a href="{{ route('facilities.show', $facility->id) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $facility->name }}
                        </a>
                    </h3>
                    <p class="text-gray-600">{{ $facility->type }}</p>
                    @if($facility->description)
                        <p class="text-gray-500 text-sm mt-1">{{ Str::limit($facility->description, 50) }}</p>
                    @endif
                    <div class="mt-2">
                        @php
                            $statusColor = '';
                            switch ($facility->status) {
                                case 'Active':
                                    $statusColor = 'bg-green-600 text-white';
                                    break;
                                case 'Booked':
                                    $statusColor = 'bg-blue-600 text-white';
                                    break;
                                case 'Maintenance':
                                    $statusColor = 'bg-yellow-500 text-white';
                                    break;
                                case 'Emergency Closure':
                                    $statusColor = 'bg-red-600 text-white';
                                    break;
                                default:
                                    $statusColor = 'bg-gray-600 text-white';
                            }
                        @endphp
                        <span class="{{ $statusColor }} py-1.5 px-3 rounded-full text-xs font-semibold">{{ $facility->status }}</span>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2">
                        <a href="{{ route('facilities.edit', $facility->id) }}" class="w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center transform hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z" />
                            </svg>
                        </a>
                        <form action="{{ route('facilities.destroy', $facility->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center transform hover:scale-110">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
