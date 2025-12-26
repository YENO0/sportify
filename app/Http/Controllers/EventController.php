<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\FacilityMaintenance;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Exceptions\InvalidEventTransitionException;
use App\Services\EventStatusService;

class EventController extends Controller
{
protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    /**
     * Display a listing of events for committee.
     */
    public function index(Request $request)
    {
        // Hardcoded committee ID - replace with authenticated committee when auth is implemented
        $committeeId = 1;

        // Committees can see their own events including drafts
        $query = Event::where('committee_id', $committeeId)
            ->withCount([
                'registrations as registrations_count' => function ($q) {
                    $q->where('status', 'registered');
                },
            ]);

        // Filter by status if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where('event_name', 'like', '%' . $request->search . '%');
        }

        $events = $query->latest()->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $events]);
        }

        return view('events.index', compact('events'));
    }

    /**
     * Display a listing of events for admin (pending and rejected events for approval).
     */
    public function adminIndex(Request $request)
    {
        $tab = $request->get('tab', 'pending'); // 'pending' (pending/rejected) or 'approved'

        $baseQuery = Event::withCount([
            'registrations as registrations_count' => function ($q) {
                $q->where('status', 'registered');
            },
        ]);

        if (\Illuminate\Support\Facades\Schema::hasTable('user')) {
            $baseQuery->with('committee');
        }

        // Apply search to both sets
        $applySearch = function ($query) use ($request) {
            if ($request->has('search') && $request->search) {
                $query->where('event_name', 'like', '%' . $request->search . '%');
            }
        };

        // Pending / Rejected
        $pendingQuery = (clone $baseQuery)->whereIn('status', ['pending', 'rejected']);
        if ($request->has('status') && $request->status !== 'all') {
            $pendingQuery->where('status', $request->status);
        }
        $applySearch($pendingQuery);
        $pendingEvents = $pendingQuery->latest()->get();

        // Approved (exclude cancelled events)
        $approvedQuery = (clone $baseQuery)->where('status', 'approved')
            ->where('event_status', '!=', 'Cancelled');
        $applySearch($approvedQuery);
        $approvedEvents = $approvedQuery->latest()->get();

        if ($request->wantsJson()) {
            return response()->json([
                'pending' => $pendingEvents,
                'approved' => $approvedEvents,
                'tab' => $tab,
            ]);
        }

        return view('admin.events.index', [
            'pendingEvents' => $pendingEvents,
            'approvedEvents' => $approvedEvents,
            'tab' => $tab,
        ]);
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(Request $request)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Create event form']);
        }

        // Get all active facilities for the dropdown
        $facilities = Facility::where('status', 'Active')->orderBy('name')->get();

        return view('events.create', compact('facilities'));
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Handle event type: if single event, set end_date = start_date
        $eventType = $request->input('event_type', 'single');
        if ($eventType === 'single' || !$request->has('event_type')) {
            $data['event_end_date'] = $data['event_start_date'];
        }

        // Enforce registration_due_date at least 1 day before start_date
        if (!empty($data['registration_due_date'])) {
            $start = \Carbon\Carbon::parse($data['event_start_date']);
            $due = \Carbon\Carbon::parse($data['registration_due_date']);
            if ($due->greaterThanOrEqualTo($start->copy()->startOfDay())) {
                return $this->errorResponse($request, 'Registration due date must be at least 1 day before the event start date.', 422);
            }
        }

        if ($request->hasFile('event_poster')) {
            $data['event_poster'] = $request->file('event_poster')
                ->store('event-posters', 'public');
        }

        // Handle facility booking if facility is selected
        $facilityId = $request->input('book_facility_id');
        if ($facilityId) {
            // Validate facility exists and is active
            $facility = Facility::where('id', $facilityId)
                ->where('status', 'Active')
                ->first();

            if (!$facility) {
                return $this->errorResponse($request, 'Selected facility is not available.', 422);
            }

            // Calculate booking start and end datetime based on event type
            $bookingStart = Carbon::parse($data['event_start_date'] . ' ' . $data['event_start_time']);
            
            if ($eventType === 'single') {
                // Single event: same date, use event times
                $bookingEnd = Carbon::parse($data['event_start_date'] . ' ' . $data['event_end_time']);
            } else {
                // Recurring event: use event end date and time
                $bookingEnd = Carbon::parse($data['event_end_date'] . ' ' . $data['event_end_time']);
            }

            // Check for conflicting bookings
            $conflictingBooking = Booking::where('facility_id', $facilityId)
                ->where('status', 'approved')
                ->where(function ($query) use ($bookingStart, $bookingEnd) {
                    $query->where(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_time', '<=', $bookingStart)
                          ->where('end_time', '>', $bookingStart);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_time', '<', $bookingEnd)
                          ->where('end_time', '>=', $bookingEnd);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_time', '>=', $bookingStart)
                          ->where('end_time', '<=', $bookingEnd);
                    });
                })->exists();

            if ($conflictingBooking) {
                return $this->errorResponse($request, 'This facility is already booked for the selected time slot.', 422);
            }

            // Check for facility maintenance overlap
            $conflictingMaintenance = FacilityMaintenance::where('facility_id', $facilityId)
                ->where(function ($query) use ($bookingStart, $bookingEnd) {
                    $query->where(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_date', '<=', $bookingStart)
                          ->where('end_date', '>', $bookingStart);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_date', '<', $bookingEnd)
                          ->where('end_date', '>=', $bookingEnd);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_date', '>=', $bookingStart)
                          ->where('end_date', '<=', $bookingEnd);
                    });
                })->exists();

            if ($conflictingMaintenance) {
                return $this->errorResponse($request, 'This facility is under maintenance during the selected time slot.', 422);
            }

            // Set facility_id in event data
            $data['facility_id'] = $facilityId;
        } else {
            // No facility selected, set to null
            $data['facility_id'] = null;
        }
        
        // Determine status: 'draft' if save_as_draft, otherwise 'pending'
        $data['status'] = $request->has('save_as_draft') ? 'draft' : 'pending';
        $data['approved_by'] = null;
        $data['approved_at'] = null;
        $data['rejection_remark'] = null;

        // Remove event_type from data as it's not a database field
        unset($data['event_type']);

        DB::beginTransaction();
        try {
            $event = Event::create($data);
            EventStatusService::sync($event);

            // Create facility booking if facility was selected
            if ($facilityId) {
                $bookingStart = Carbon::parse($data['event_start_date'] . ' ' . $data['event_start_time']);
                
                if ($eventType === 'single') {
                    $bookingEnd = Carbon::parse($data['event_start_date'] . ' ' . $data['event_end_time']);
                } else {
                    $bookingEnd = Carbon::parse($data['event_end_date'] . ' ' . $data['event_end_time']);
                }

                Booking::create([
                    'facility_id' => $facilityId,
                    'user_id' => Auth::id() ?? $data['committee_id'],
                    'start_time' => $bookingStart,
                    'end_time' => $bookingEnd,
                    'status' => 'approved', // Auto-approve bookings for events
                ]);
            }

            DB::commit();

            $message = $data['status'] === 'draft' 
                ? 'Event saved as draft successfully.' 
                : 'Event applied successfully.';

            if ($facilityId) {
                $message .= ' Facility booking created successfully.';
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'data' => $event,
                ], 201);
            }

            return redirect()->route('committee.events.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create event: ' . $e->getMessage());
            
            return $this->errorResponse($request, 'Failed to create event. Please try again.', 500);
        }
    }

    /**
     * Show the form for editing an event.
     */
    public function edit(Request $request, Event $event)
    {
        // Hardcoded committee ID - replace with authenticated committee when auth is implemented
        $committeeId = 1;
        
        // Prevent editing approved events
        if ($event->status === 'approved') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Approved events cannot be edited.'], 403);
            }
            return redirect()->route('committee.events.index')->with('error', 'Approved events cannot be edited.');
        }
        
        // Only allow committee to edit their own events
        if ($event->committee_id != $committeeId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            return redirect()->route('committee.events.index')->with('error', 'Unauthorized.');
        }

        if ($request->wantsJson()) {
            return response()->json(['data' => $event]);
        }

        // Facilities for "Book Facility" selector (include current facility even if not Active)
        $facilities = Facility::orderBy('name')->get();

        return view('events.edit', compact('event', 'facilities'));
    }

    /**
     * Update an event.
     */
    public function update(Request $request, Event $event)
    {
        // Hardcoded committee ID - replace with authenticated committee when auth is implemented
        $committeeId = 1;
        
        // Prevent editing approved events
        if ($event->status === 'approved') {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Approved events cannot be edited.'], 403);
            }
            return redirect()->route('committee.events.index')->with('error', 'Approved events cannot be edited.');
        }
        
        // Only allow committee to edit their own events
        if ($event->committee_id != $committeeId) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 403);
            }
            return redirect()->route('committee.events.index')->with('error', 'Unauthorized.');
        }
        
        $data = $this->validateData($request, isUpdate: true);

        // When the form only sends the action button (e.g. "apply_event") we won't
        // receive all fields. Fill missing validated fields with the event's
        // current values so downstream logic (date comparisons, status changes)
        // has the data it expects.
        $data = array_merge(
            $event->only([
                'event_name',
                'event_description',
                'event_poster',
                'event_start_date',
                'event_start_time',
                'event_end_date',
                'event_end_time',
                'registration_due_date',
                'max_capacity',
                'price',
                'facility_id',
                'committee_id',
            ]),
            $data
        );
        
        // Handle event type: if single event, set end_date = start_date
        if ($request->input('event_type') === 'single' || !$request->has('event_type')) {
            $data['event_end_date'] = $data['event_start_date'];
        }

        // Enforce registration_due_date at least 1 day before start_date
        if (!empty($data['registration_due_date'])) {
            $start = \Carbon\Carbon::parse($data['event_start_date']);
            $due = \Carbon\Carbon::parse($data['registration_due_date']);
            if ($due->greaterThanOrEqualTo($start->copy()->startOfDay())) {
                return $this->errorResponse($request, 'Registration due date must be at least 1 day before the event start date.', 422);
            }
        }

        if ($request->hasFile('event_poster')) {
            $data['event_poster'] = $request->file('event_poster')
                ->store('event-posters', 'public');
        }

        // Handle facility booking (same contract as create: book_facility_id)
        $eventType = $request->input('event_type', 'single');
        $facilityId = $request->input('book_facility_id');

        // Compute the old booking window to locate an existing booking for this event
        $oldStart = Carbon::parse($event->event_start_date . ' ' . ($event->event_start_time ?? '00:00'));
        $oldEndDate = $event->event_end_date ?: $event->event_start_date;
        $oldEnd = Carbon::parse($oldEndDate . ' ' . ($event->event_end_time ?? '00:00'));

        $existingBooking = null;
        if ($event->facility_id) {
            $existingBooking = Booking::where('facility_id', $event->facility_id)
                ->where('user_id', $event->committee_id)
                ->whereIn('status', ['approved', 'pending'])
                ->where('start_time', $oldStart)
                ->where('end_time', $oldEnd)
                ->latest('id')
                ->first();
        }

        if ($facilityId) {
            // Validate facility exists and is active
            $facility = Facility::where('id', $facilityId)
                ->where('status', 'Active')
                ->first();

            if (!$facility) {
                return $this->errorResponse($request, 'Selected facility is not available.', 422);
            }

            // Calculate booking start and end datetime based on event type
            $bookingStart = Carbon::parse($data['event_start_date'] . ' ' . $data['event_start_time']);

            if ($eventType === 'single') {
                $bookingEnd = Carbon::parse($data['event_start_date'] . ' ' . $data['event_end_time']);
            } else {
                $bookingEnd = Carbon::parse($data['event_end_date'] . ' ' . $data['event_end_time']);
            }

            if ($bookingEnd->lessThanOrEqualTo($bookingStart)) {
                return $this->errorResponse($request, 'Event end time must be after start time.', 422);
            }

            // Check for conflicting bookings (exclude this event's existing booking if found)
            $conflictingBooking = Booking::where('facility_id', $facilityId)
                ->where('status', 'approved')
                ->when($existingBooking, function ($q) use ($existingBooking) {
                    $q->where('id', '!=', $existingBooking->id);
                })
                ->where(function ($query) use ($bookingStart, $bookingEnd) {
                    $query->where(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_time', '<=', $bookingStart)
                          ->where('end_time', '>', $bookingStart);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_time', '<', $bookingEnd)
                          ->where('end_time', '>=', $bookingEnd);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_time', '>=', $bookingStart)
                          ->where('end_time', '<=', $bookingEnd);
                    });
                })->exists();

            if ($conflictingBooking) {
                return $this->errorResponse($request, 'This facility is already booked for the selected time slot.', 422);
            }

            // Check for facility maintenance overlap
            $conflictingMaintenance = FacilityMaintenance::where('facility_id', $facilityId)
                ->where(function ($query) use ($bookingStart, $bookingEnd) {
                    $query->where(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_date', '<=', $bookingStart)
                          ->where('end_date', '>', $bookingStart);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_date', '<', $bookingEnd)
                          ->where('end_date', '>=', $bookingEnd);
                    })->orWhere(function ($q) use ($bookingStart, $bookingEnd) {
                        $q->where('start_date', '>=', $bookingStart)
                          ->where('end_date', '<=', $bookingEnd);
                    });
                })->exists();

            if ($conflictingMaintenance) {
                return $this->errorResponse($request, 'This facility is under maintenance during the selected time slot.', 422);
            }

            // Store selected facility on the event
            $data['facility_id'] = $facilityId;
        } else {
            $data['facility_id'] = null;
        }

        // Remove event_type from data as it's not a database field
        unset($data['event_type']);

        DB::beginTransaction();
        // Handle status changes
        try {
            if ($request->has('save_as_draft')) {
                $data['status'] = 'draft';
                $event->update($data);
            } elseif ($request->has('apply_event') && $event->status === 'draft') {
                $data['status'] = 'pending';
                $event->update($data);
            } elseif ($event->status === 'rejected') {
                // If event is rejected, resubmit it (resubmit already updates)
                $event = $event->state()->resubmit($data);
                $data['status'] = $event->status;
            } else {
                // Keep current status if not changing
                $data['status'] = $event->status;
                $event->update($data);
            }

            // Update/Create/Delete related facility booking
            if ($existingBooking) {
                // If facility removed, delete the booking; otherwise update it to the new slot/facility
                if (!$facilityId) {
                    $existingBooking->delete();
                } else {
                    $bookingStart = Carbon::parse($data['event_start_date'] . ' ' . $data['event_start_time']);
                    $bookingEnd = ($eventType === 'single')
                        ? Carbon::parse($data['event_start_date'] . ' ' . $data['event_end_time'])
                        : Carbon::parse($data['event_end_date'] . ' ' . $data['event_end_time']);

                    $existingBooking->update([
                        'facility_id' => $facilityId,
                        'start_time' => $bookingStart,
                        'end_time' => $bookingEnd,
                        'status' => 'approved',
                    ]);
                }
            } elseif ($facilityId) {
                // No existing booking found; create a new one
                $bookingStart = Carbon::parse($data['event_start_date'] . ' ' . $data['event_start_time']);
                $bookingEnd = ($eventType === 'single')
                    ? Carbon::parse($data['event_start_date'] . ' ' . $data['event_end_time'])
                    : Carbon::parse($data['event_end_date'] . ' ' . $data['event_end_time']);

                Booking::create([
                    'facility_id' => $facilityId,
                    'user_id' => Auth::id() ?? $event->committee_id,
                    'start_time' => $bookingStart,
                    'end_time' => $bookingEnd,
                    'status' => 'approved',
                ]);
            }

            DB::commit();
        } catch (InvalidEventTransitionException $e) {
            DB::rollBack();
            return $this->errorResponse($request, $e->getMessage(), 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update event: ' . $e->getMessage());
            return $this->errorResponse($request, 'Failed to update event. Please try again.', 500);
        }

        EventStatusService::sync($event);

        $message = 'Event updated successfully.';
        if ($data['status'] === 'draft') {
            $message = 'Event saved as draft successfully.';
        } elseif ($data['status'] === 'pending' && $event->status === 'draft') {
            $message = 'Event applied successfully.';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'data' => $event,
            ]);
        }

        return redirect()->route('committee.events.index')->with('success', $message);
    }

    /**
     * Delete an event.
     */
    public function destroy(Request $request, Event $event)
    {
        $event->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Event deleted successfully.',
            ]);
        }

        return redirect()->route('committee.events.index')->with('success', 'Event deleted successfully.');
    }

    /**
     * Cancel an event application (mark as rejected with a remark).
     */
    public function cancel(Request $request, Event $event)
    {
        $remark = $request->input('rejection_remark', 'Cancelled by requester.');

        try {
            $event = $event->state()->cancel($remark);
        } catch (InvalidEventTransitionException $e) {
            return $this->errorResponse($request, $e->getMessage(), 422);
        }

        EventStatusService::sync($event);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Event application cancelled.',
                'data' => $event,
            ]);
        }

        return redirect()->back()->with('success', 'Event application cancelled.');
    }

    /**
     * Approve a pending event.
     */
    public function approve(Request $request, Event $event)
    {
        $approverId = optional($request->user())->id ?? $request->input('approved_by');

        try {
            $event = $event->state()->approve($approverId);
        } catch (InvalidEventTransitionException $e) {
            return $this->errorResponse($request, $e->getMessage(), 422);
        }

        EventStatusService::sync($event);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Event approved.',
                'data' => $event,
            ]);
        }

        return redirect()->back()->with('success', 'Event approved.');
    }

    /**
     * Reject a pending event with a remark.
     */
    public function reject(Request $request, Event $event)
    {
        $data = $request->validate([
            'rejection_remark' => ['required', 'string'],
            'approved_by' => ['nullable', 'integer'],
        ]);

        $approverId = optional($request->user())->id ?? $data['approved_by'] ?? null;

        try {
            $event = $event->state()->reject($data['rejection_remark'], $approverId);
        } catch (InvalidEventTransitionException $e) {
            return $this->errorResponse($request, $e->getMessage(), 422);
        }

        EventStatusService::sync($event);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Event rejected.',
                'data' => $event,
            ], 200);
        }

        return redirect()->back()->with('success', 'Event rejected.');
    }

    /**
     * Shared validation rules.
     */
    protected function validateData(Request $request, bool $isUpdate = false): array
    {
        $requiredRules = $isUpdate ? ['sometimes', 'required'] : ['required'];
        
        // Calculate minimum date (1 week from today)
        $minDate = now()->addWeek()->format('Y-m-d');
        
        // Get event type to determine validation rules
        $eventType = $request->input('event_type', 'single');
        
        // Build end_date validation rules
        $endDateRules = [
            'nullable',
            'date',
            'after_or_equal:' . $minDate,
        ];
        
        // For recurring events, end_date must be after (not equal to) start_date
        if ($eventType === 'recurring') {
            $endDateRules[] = 'required';
            $endDateRules[] = 'after:event_start_date';
        } else {
            $endDateRules[] = 'after_or_equal:event_start_date';
        }

        return $request->validate([
            'event_name' => array_merge($requiredRules, ['string', 'max:255']),
            'event_description' => ['nullable', 'string'],
            'event_poster' => [$isUpdate ? 'sometimes' : 'nullable', 'file', 'image', 'max:2048'],
            'event_start_date' => array_merge($requiredRules, [
                'date',
                'after_or_equal:' . $minDate,
            ]),
            'event_start_time' => array_merge($requiredRules, ['date_format:H:i']),
            'event_end_date' => $endDateRules,
            'event_end_time' => ['nullable', 'date_format:H:i'],
            'registration_due_date' => ['nullable', 'date', 'before_or_equal:event_start_date'],
            'max_capacity' => array_merge($requiredRules, ['integer', 'min:1']),
            'price' => array_merge($requiredRules, ['numeric', 'min:0']),
            'facility_id' => ['nullable', 'integer', 'exists:facilities,id'],
            'book_facility_id' => ['nullable', 'integer', 'exists:facilities,id'],
            'committee_id' => array_merge($requiredRules, ['integer']),
            'event_type' => ['nullable', 'in:single,recurring'],
        ], [
            'event_start_date.after_or_equal' => 'The event start date must be at least 1 week from today.',
            'event_end_date.after_or_equal' => 'The event end date must be at least 1 week from today and cannot be in the past.',
            'event_end_date.required' => 'The end date is required for recurring events.',
            'event_end_date.after' => 'For recurring events, the end date must be after the start date (cannot be the same day).',
            'max_capacity.required' => 'The capacity field is required.',
            'max_capacity.integer' => 'The capacity must be a whole number.',
            'max_capacity.min' => 'The capacity must be at least 1. Cannot be 0.',
            'registration_due_date.before_or_equal' => 'Registration due date must be on or before the event start date (and at least 1 day before).',
        ]);
    }

    /**
     * Helper to return either JSON or redirect with an error.
     */
    protected function errorResponse(Request $request, string $message, int $status = 400)
    {
        if ($request->wantsJson()) {
            return response()->json(['message' => $message], $status);
        }

        return redirect()->back()->withErrors(['error' => $message]);
    }

    /**
     * List approved events for students to browse/register.
     */
    public function approved(Request $request)
    {
        // Use authenticated student id when available
        $studentId = Auth::id() ?? 1;

        // Keep lifecycle/registration statuses current before listing
        EventStatusService::syncAll();

        // Students only see approved events that are upcoming or ongoing (exclude cancelled)
        $query = Event::where('status', 'approved')
            ->where('event_status', '!=', 'Cancelled')
            ->whereIn('event_status', ['Upcoming', 'Ongoing'])
            ->withCount([
                'registrations as registrations_count' => function ($q) {
                    $q->where('status', 'registered');
                },
            ]);

        // Filter by time period
        // Default to "this_week" instead of "all"
        $filter = $request->get('filter', 'this_week');
        $now = now();
        
        if ($filter === 'this_week') {
            $startOfWeek = $now->copy()->startOfWeek();
            $endOfWeek = $now->copy()->endOfWeek();
            $query->whereBetween('event_start_date', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
        } elseif ($filter === 'this_month') {
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();
            $query->whereBetween('event_start_date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')]);
        }
        // 'all' shows all events, no date filtering

        // Only eager load committee if user table exists (temporary until integration)
        if (\Illuminate\Support\Facades\Schema::hasTable('user')) {
            $query->with('committee');
        }

        $events = $query->orderBy('event_start_date', 'asc')->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $events]);
        }

        return view('events.approved', compact('events', 'studentId', 'filter'));
    }

    /**
     * Show event details page.
     * Students can only see approved events.
     * Committees can see their own events (any status).
     */
    public function show(Request $request, Event $event)
    {
        $event = EventStatusService::sync($event);
        // Hardcoded IDs - replace with authenticated user when auth is implemented
        $studentId = Auth::id() ?? 1;
        $committeeId = 1;
        $isAdmin = $request->routeIs('admin.*') || $request->has('admin'); // Check if admin route or query param

        // Check access permissions
        $canView = false;
        $isCommitteeView = false;
        $isAdminView = false;
        
        // Check if admin first
        if ($isAdmin) {
            // Admins can view all events
            $canView = true;
            $isAdminView = true;
        } elseif ($event->status === 'approved') {
            // Approved events are visible to everyone (students and committees)
            $canView = true;
        } elseif ($event->committee_id == $committeeId) {
            // Committees can view their own events (draft, pending, rejected)
            $canView = true;
            $isCommitteeView = true;
        }

        if (!$canView) {
            abort(404);
        }

        $event->loadCount([
            'registrations as registrations_count' => function ($q) {
                $q->where('status', 'registered');
            },
        ]);

        // Check if user table exists before loading committee
        if (\Illuminate\Support\Facades\Schema::hasTable('user')) {
            $event->load('committee');
        }
        // Load facility name for display (Event -> facility_id)
        if (\Illuminate\Support\Facades\Schema::hasTable('facilities')) {
            $event->load('facility');
        }

        // Check if student is already registered (only for approved events)
        $isRegistered = false;
        if ($event->status === 'approved' && \Illuminate\Support\Facades\Schema::hasTable('eventJoined')) {
            $isRegistered = \App\Models\EventJoined::where('eventID', $event->eventID)
                ->where('studentID', $studentId)
                ->where('status', 'registered')
                ->exists();
        }

        $registered = $event->registrations_count ?? 0;
        $remaining = max(0, $event->max_capacity - $registered);
        
        // Load equipment borrowings for committees and admins
        $equipmentBorrowings = null;
        if ($isCommitteeView || $isAdminView) {
            $equipmentBorrowings = $event->equipmentBorrowings()
                ->with(['equipment:id,name,brand_id', 'equipment.brand:id,name'])
                ->where('status', 'borrowed')
                ->get();
        }
        
        // Load student details if viewing as student
        $student = null;
        if ($event->status === 'approved' && !$isCommitteeView && !$isAdminView) {
            if (\Illuminate\Support\Facades\Schema::hasTable('students')) {
                $student = \App\Models\Student::find($studentId);
            }
        }
        
        // Calculate days left for registration
        $daysLeft = null;
        if ($event->registration_due_date) {
            $dueDate = \Carbon\Carbon::parse($event->registration_due_date);
            $daysLeft = max(0, now()->diffInDays($dueDate, false));
        }

        if ($isCommitteeView) {
            // Committee-specific view (no reserve slot UI)
            $registrations = $event->registrations()
                ->with(['user:id,name,email,contact'])
                ->orderBy('joinedDate')
                ->get(['eventJoinedID', 'eventID', 'studentID', 'status', 'joinedDate']);

            if ($request->wantsJson()) {
                return response()->json([
                    'data' => [
                        'event' => $event,
                        'registrations' => $registrations,
                    ],
                ]);
            }

            return view('committee.events.show', compact('event', 'registrations'));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $event,
                'remaining' => $remaining,
                'is_registered' => $isRegistered,
            ]);
        }

        return view('events.show', compact('event', 'remaining', 'isRegistered', 'studentId', 'isCommitteeView', 'isAdminView', 'student', 'daysLeft', 'equipmentBorrowings'));
    }

    /**
     * Committee view: show event details and participants.
     */
    public function committeeShow(Request $request, Event $event)
    {
        $event = EventStatusService::sync($event);
        if (\Illuminate\Support\Facades\Schema::hasTable('facilities')) {
            $event->load('facility');
        }
        $event->loadCount([
            'registrations as registrations_count' => function ($q) {
                $q->where('status', 'registered');
            },
        ]);

        $registrations = $event->registrations()
            ->with(['user:id,name,email,contact'])
            ->orderBy('joinedDate')
            ->get(['eventJoinedID', 'eventID', 'studentID', 'status', 'joinedDate']);

        // Load equipment borrowings with equipment details
        $equipmentBorrowings = $event->equipmentBorrowings()
            ->with(['equipment:id,name'])
            ->where('status', 'borrowed')
            ->get();

        $registered = $event->registrations_count ?? 0;
        $remaining = max(0, $event->max_capacity - $registered);
        $isRegistered = false; // committees don't register for their own events
        $studentId = null;
        $student = null;
        $isCommitteeView = true;
        $isAdminView = false;
        $daysLeft = $event->registration_due_date
            ? now()->diffInDays(\Carbon\Carbon::parse($event->registration_due_date), false)
            : null;

        if ($request->wantsJson()) {
            return response()->json([
                'data' => [
                    'event' => $event,
                    'registrations' => $registrations,
                    'remaining' => $remaining,
                ],
            ]);
        }

        return view('events.show', compact(
            'event',
            'registrations',
            'remaining',
            'isRegistered',
            'studentId',
            'isCommitteeView',
            'isAdminView',
            'student',
            'daysLeft',
            'equipmentBorrowings'
        ));
    }
}
