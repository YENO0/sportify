@extends('layouts.app')

@section('title', $facility->name . ' Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="flex flex-col md:flex-row">
            <div class="md:w-1/2">
                @if($facility->image)
                    <img src="{{ route('facilities.photo', ['filename' => basename($facility->image)]) }}" alt="{{ $facility->name }}" class="w-full h-auto rounded-lg shadow-lg">
                @else
                    <div class="w-full h-64 bg-gray-200 flex items-center justify-center rounded-lg shadow-lg">
                        <span class="text-gray-500 text-xl">No Image Available</span>
                    </div>
                @endif
            </div>
            <div class="md:w-1/2 md:pl-8 mt-6 md:mt-0">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $facility->name }}</h1>
                <p class="text-gray-600 text-lg mb-4">{{ $facility->type }}</p>

                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Description</h2>
                    <p class="text-gray-700">{{ $facility->description ?? 'No description provided.' }}</p>
                </div>

                <div class="mb-4">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Status</h2>
                    @php
                        $statusColor = '';
                        switch ($facility->status) {
                            case 'Active':
                                $statusColor = 'bg-green-200 text-green-800';
                                break;
                            case 'Booked':
                                $statusColor = 'bg-blue-200 text-blue-800';
                                break;
                            case 'Maintenance':
                                $statusColor = 'bg-yellow-200 text-yellow-800';
                                break;
                            case 'Emergency Closure':
                                $statusColor = 'bg-red-200 text-red-800';
                                break;
                            default:
                                $statusColor = 'bg-gray-200 text-gray-800';
                        }
                    @endphp
                    <span class="{{ $statusColor }} py-1 px-3 rounded-full text-sm font-medium">{{ $facility->status }}</span>
                </div>

                <div class="mt-6">
                    <a href="{{ route('facilities.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Facilities
                    </a>
                    <a href="{{ route('facilities.edit', $facility->id) }}" class="ml-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.5L16.732 3.732z"></path></svg>
                        Edit Facility
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
