@extends('layouts.app')

@section('title', 'Facility Timetable')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Facility Timetable</h1>

    <form method="GET" action="{{ route('facilities.timetable') }}" class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-4 mb-6">
        <div class="w-full md:w-1/3">
            <label for="facility_id" class="block text-gray-700 font-medium mb-1">Select Facility:</label>
            <select name="facility_id" id="facility_id" class="form-select block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" onchange="this.form.submit()">
                <option value="">Select a Facility</option>
                @foreach($allFacilities as $facility)
                    <option value="{{ $facility->id }}" {{ $selectedFacility && $selectedFacility->id == $facility->id ? 'selected' : '' }}>
                        {{ $facility->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="w-full md:w-1/3">
            <label for="start_date" class="block text-gray-700 font-medium mb-1">Start Date:</label>
            <input type="date" name="start_date" id="start_date" 
                   value="{{ isset($startDate) ? $startDate->format('Y-m-d') : date('Y-m-d') }}"
                   class="form-input block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                   onchange="this.form.submit()">
        </div>
    </form>

    @if(!$selectedFacility)
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
            <p class="font-bold">Please select a facility to view its timetable.</p>
        </div>
    @else
        <h2 class="text-2xl font-semibold mb-4 text-gray-700">{{ $selectedFacility->name }} Timetable</h2>

        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 w-24">Time</th>
                        @foreach($days as $day)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">
                                {{ $day->format('D, M d') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($hours as $hour)
                        <tr>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-gray-900 sticky left-0 bg-white z-10 w-24">{{ $hour }}</td>
                            @foreach($days as $day)
                                @php
                                    $currentSlot = $timetableData[$day->format('Y-m-d')][$hour] ?? null;
                                    $slotStatus = $currentSlot['status'] ?? 'available';
                                    $isPast = $day->copy()->setTime((int)substr($hour, 0, 2), 0)->isPast();

                                    $bgColor = '';
                                    $textColor = '';
                                    $tooltip = '';

                                    if ($slotStatus === 'booked') {
                                        $bgColor = 'bg-red-200';
                                        $textColor = 'text-red-800';
                                        $booking = $currentSlot['booking'];
                                        $tooltip = "Booked by: {$booking->user->name} ({$booking->status})";
                                    } elseif ($slotStatus === 'maintenance') {
                                        $bgColor = 'bg-yellow-200';
                                        $textColor = 'text-yellow-800';
                                        $maintenance = $currentSlot['maintenance'];
                                        $tooltip = "Maintenance: {$maintenance->title}";
                                    } elseif ($isPast) {
                                        $bgColor = 'bg-gray-100';
                                        $textColor = 'text-gray-400';
                                        $tooltip = 'Past';
                                    } else {
                                        $bgColor = 'bg-green-100';
                                        $textColor = 'text-green-800';
                                        $tooltip = 'Available';
                                    }
                                @endphp
                                <td class="px-2 py-1 border text-center relative {{ $bgColor }} {{ $textColor }} cursor-help group text-xs w-36 h-12">
                                    <span class="truncate block">
                                        @if ($slotStatus === 'booked')
                                            Booked
                                        @elseif ($slotStatus === 'maintenance')
                                            Maint.
                                        @else
                                            &nbsp; <!-- Non-breaking space for available slots -->
                                        @endif
                                    </span>
                                    @if($tooltip)
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden w-48 p-2 text-xs text-white bg-gray-700 rounded shadow-lg group-hover:block z-20">
                                            {{ $tooltip }}
                                            @if($slotStatus === 'booked' && $currentSlot['booking'])
                                                <br> {{ $currentSlot['booking']->start_time->format('H:i') }} - {{ $currentSlot['booking']->end_time->format('H:i') }}
                                            @elseif($slotStatus === 'maintenance' && $currentSlot['maintenance'])
                                                <br> {{ $currentSlot['maintenance']->start_date->format('H:i') }} - {{ $currentSlot['maintenance']->end_date->format('H:i') }}
                                            @endif
                                            <div class="absolute w-3 h-3 bg-gray-700 transform rotate-45 -bottom-1 left-1/2 -translate-x-1/2"></div>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
