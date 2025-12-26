<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Equipment;
use App\Services\EquipmentBorrowingService;
use App\Patterns\Factory\BorrowingFactoryManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipmentBorrowingController extends Controller
{
    protected $borrowingService;

    public function __construct(EquipmentBorrowingService $borrowingService)
    {
        $this->borrowingService = $borrowingService;
    }
    /**
     * Show the form for borrowing equipment for an event.
     */
    public function create(Event $event)
    {
        try {
            $equipment = $this->borrowingService->getAvailableEquipment();
            $borrowedEquipment = $this->borrowingService->getBorrowedEquipmentIds($event->eventID);
            
            return view('equipment-borrowings.create', compact('event', 'equipment', 'borrowedEquipment'));
        } catch (\Exception $e) {
            Log::error('Error loading borrow form: ' . $e->getMessage());
            return redirect()->route('events.index')->with('error', 'Failed to load borrow form.');
        }
    }

    /**
     * Store a newly created borrowing.
     */
    public function store(Request $request, Event $event)
    {
        try {
            $validated = $request->validate([
                'equipment' => 'required|array|min:1',
                'equipment.*' => 'required|exists:equipment,id|distinct',
                'quantity' => 'required|array|min:1',
                'quantity.*' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            $result = $this->borrowingService->createBorrowings($event->eventID, $validated);
            
            return redirect()->route('committee.events.show', $event->eventID)
                ->with('success', $result['message']);
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error creating borrowing: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to borrow equipment. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified borrowing (manual return).
     */
    public function destroy(Event $event, $borrowingId)
    {
        try {
            $this->borrowingService->returnBorrowing($event->eventID, $borrowingId);
            
            return redirect()->route('committee.events.show', $event->eventID)
                ->with('success', 'Equipment returned successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error returning equipment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to return equipment.');
        }
    }
}

