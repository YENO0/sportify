<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    /**
     * Display a listing of events.
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'status', 'sort', 'direction']);
            $events = $this->eventService->getAll($filters, 15);
            $events->appends($request->query());
            
            return view('events.index', compact('events'));
        } catch (\Exception $e) {
            Log::error('Error loading events: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load events.');
        }
    }

    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        return view('events.create');
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_date' => 'required|date|after_or_equal:start_date',
                'end_time' => 'required|date_format:H:i',
            ]);

            $event = $this->eventService->create($validated);
            
            return redirect()->route('events.show', $event->id)
                ->with('success', 'Event created successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error creating event: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create event. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event)
    {
        try {
            $event = $this->eventService->getById($event->id);
            return view('events.show', compact('event'));
        } catch (\Exception $e) {
            Log::error('Error loading event: ' . $e->getMessage());
            return redirect()->route('events.index')->with('error', 'Failed to load event.');
        }
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_date' => 'required|date|after_or_equal:start_date',
                'end_time' => 'required|date_format:H:i',
            ]);

            $this->eventService->update($event->id, $validated);
            
            return redirect()->route('events.show', $event->id)
                ->with('success', 'Event updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error updating event: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update event. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event)
    {
        try {
            $this->eventService->delete($event->id);
            return redirect()->route('events.index')
                ->with('success', 'Event deleted successfully, and all borrowed equipment returned.');
        } catch (\Exception $e) {
            Log::error('Error deleting event: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete event.');
        }
    }

    /**
     * Test button: Change event end date/time to past and process returns
     */
    public function testReturn(Event $event)
    {
        try {
            $result = $this->eventService->testReturnLogic($event->id);
            
            return redirect()->route('events.show', $event->id)
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            Log::error('Error testing return: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to test return. ' . $e->getMessage());
        }
    }

    /**
     * Process automatic returns for all events that have ended
     */
    public function processAutomaticReturns()
    {
        try {
            $result = $this->eventService->processAutomaticReturns();
            
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'returned_count' => $result['total_returned']
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing automatic returns: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process automatic returns: ' . $e->getMessage()
            ], 500);
        }
    }
}

