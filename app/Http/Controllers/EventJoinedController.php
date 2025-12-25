<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventJoined;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EventStatusService;

class EventJoinedController extends Controller
{
    /**
     * Register a student for an approved event.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'event_id' => ['required', 'integer', 'exists:events,eventID'],
        ]);

        $event = Event::find($data['event_id']);

        if (
            $event->status !== 'approved' ||
            !in_array($event->event_status, ['Upcoming', 'Ongoing']) ||
            $event->registration_status !== 'Open'
        ) {
            return $this->errorResponse($request, 'Event is not open for registration.', 422);
        }

        // Hardcoded: replace with authenticated student id when available
        $studentId = 1;

        $exists = EventJoined::where('eventID', $event->eventID)
            ->where('studentID', $studentId)
            ->exists();

        if ($exists) {
            return $this->errorResponse($request, 'You are already registered for this event.', 409);
        }

        $registeredCount = EventJoined::where('eventID', $event->eventID)
            ->where('status', 'registered')
            ->count();

        $status = $registeredCount >= $event->max_capacity ? 'waitlisted' : 'registered';

        $registration = EventJoined::create([
            'eventID' => $event->eventID,
            'studentID' => $studentId,
            'paymentID' => null, // Placeholder; replace when payment module is ready
            'status' => $status,
            // joinedDate uses DB default; no need to set
        ]);

        // Recalculate registration status (may become full)
        EventStatusService::sync($event);

        $message = $status === 'registered'
            ? 'Registration successful.'
            : 'Event is full; you have been waitlisted.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $registration,
            ], 201);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Cancel a registration (do not delete).
     */
    public function cancel(EventJoined $eventJoined, Request $request)
    {
        $eventJoined->update(['status' => 'cancelled']);
        if ($event = Event::find($eventJoined->eventID)) {
            EventStatusService::sync($event);
        }

        $message = 'Registration cancelled.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $eventJoined->fresh(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * List registrations for an event (committee/admin view).
     */
    public function index(int $eventId)
    {
        $registrations = EventJoined::where('eventID', $eventId)
            ->orderBy('joinedDate')
            ->get(['studentID', 'status', 'joinedDate']);

        return response()->json([
            'data' => $registrations,
        ]);
    }

    protected function errorResponse(Request $request, string $message, int $status = 400)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return redirect()->back()->withErrors(['error' => $message]);
    }
}
