<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility; // Import Facility model
use Illuminate\Http\Request;
use Carbon\Carbon; // Import Carbon

class FacilityTimetableController extends Controller
{
    public function index(Request $request)
    {
        $allFacilities = Facility::all();
        $selectedFacilityId = $request->input('facility_id');
        
        // Get start date from request or default to today
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::today();
        
        $selectedFacility = null;
        $bookings = collect(); // Initialize as an empty collection

        // Define the date range (e.g., next 7 days)
        $days = [];
        for ($i = 0; $i < 7; $i++) {
            $days[] = (clone $startDate)->addDays($i);
        }

        // Define the hour range (e.g., 00:00 to 23:00)
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[] = sprintf('%02d:00', $i);
        }

        if ($selectedFacilityId) {
            $selectedFacility = Facility::find($selectedFacilityId);
            if ($selectedFacility) {
                // Fetch bookings for the selected facility within the defined date range
                $bookings = Booking::where('facility_id', $selectedFacility->id)
                    ->whereIn('status', ['approved', 'pending']) // Consider approved and pending as blocking
                    ->whereBetween('start_time', [(clone $startDate)->startOfDay(), (clone $startDate)->addDays(7)->endOfDay()])
                    ->orderBy('start_time')
                    ->get();
            }
        }

        // Prepare a grid structure for the view
        $timetableData = [];
        foreach ($days as $day) {
            foreach ($hours as $hour) {
                $timeSlot = Carbon::parse($day->format('Y-m-d') . ' ' . $hour);
                $timetableData[$day->format('Y-m-d')][$hour] = [
                    'status' => 'available', // Default status
                    'booking' => null,
                    'maintenance' => null,
                ];

                // Check for maintenance overlap
                $maintenance = $selectedFacility ?
                    \App\Models\FacilityMaintenance::where('facility_id', $selectedFacility->id)
                        ->where('start_date', '<=', $timeSlot)
                        ->where('end_date', '>', $timeSlot)
                        ->first() : null;

                if ($maintenance) {
                    $timetableData[$day->format('Y-m-d')][$hour]['status'] = 'maintenance';
                    $timetableData[$day->format('Y-m-d')][$hour]['maintenance'] = $maintenance;
                }

                // Check for booking overlap
                foreach ($bookings as $booking) {
                    // Check if the current time slot falls within a booking period
                    if ($timeSlot->gte($booking->start_time) && $timeSlot->lt($booking->end_time)) {
                        // If already marked as maintenance, maintenance takes precedence
                        if ($timetableData[$day->format('Y-m-d')][$hour]['status'] !== 'maintenance') {
                            $timetableData[$day->format('Y-m-d')][$hour]['status'] = 'booked';
                            $timetableData[$day->format('Y-m-d')][$hour]['booking'] = $booking;
                        }
                    }
                }
            }
        }

        return view('facilities.timetable', compact(
            'allFacilities',
            'selectedFacility',
            'days',
            'hours',
            'timetableData',
            'startDate'
        ));
    }
}