<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Facility;
use App\Models\FacilityMaintenance; // Import FacilityMaintenance model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\FacilityBookingException;
use Carbon\Carbon; // Import Carbon

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())->get();
        return view('bookings.index', compact('bookings'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $facilities = Facility::where('status', 'Active')->get();
        return view('bookings.create', compact('facilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'start_time' => [
                'required',
                'date',
                'after:now', // Past Date Restriction
                'before_or_equal:' . Carbon::now()->addDays(90)->format('Y-m-d H:i:s'), // 3-Month Booking Window
            ],
            'end_time' => 'required|date|after:start_time',
        ]);

        // Check for conflicting bookings
        $conflictingBooking = Booking::where('facility_id', $request->facility_id)
            ->where('status', 'approved') // Only check against 'approved' bookings
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '<=', $request->start_time)
                      ->where('end_time', '>', $request->start_time);
                })->orWhere(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>=', $request->end_time);
                });
            })->exists();

        if ($conflictingBooking) {
            throw new FacilityBookingException('This time slot is already booked.'); // Conflict Prevention message
        }

        // Check for facility maintenance overlap
        $conflictingMaintenance = FacilityMaintenance::where('facility_id', $request->facility_id)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_date', '<=', $request->start_time)
                      ->where('end_date', '>', $request->start_time);
                })->orWhere(function ($q) use ($request) {
                    $q->where('start_date', '<', $request->end_time)
                      ->where('end_date', '>=', $request->end_time);
                });
            })->exists();

        if ($conflictingMaintenance) {
            throw new FacilityBookingException('This facility is under maintenance during the selected time slot.');
        }

        Booking::create([
            'facility_id' => $request->facility_id,
            'user_id' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'approved',
        ]);

        return redirect()->route('bookings.index')
                         ->with('success', 'Booking created successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        if ($booking->user_id != Auth::id()) {
            abort(403);
        }

        try {
            $booking->delete();
            return redirect()->route('bookings.index')
                             ->with('success', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            throw new FacilityBookingException("Booking not found or could not be cancelled.");
        }
    }
}
