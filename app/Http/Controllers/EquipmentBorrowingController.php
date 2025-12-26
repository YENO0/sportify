<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Equipment;
use App\Models\EquipmentBorrowing;
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
     * Show the form for editing a borrowing.
     */
    public function edit(Event $event, EquipmentBorrowing $borrowing)
    {
        try {
            // Verify the borrowing belongs to this event
            if ($borrowing->event_id != $event->eventID) {
                return redirect()->route('committee.events.show', $event->eventID)
                    ->with('error', 'Borrowing not found for this event.');
            }
            
            if ($borrowing->isReturned()) {
                return redirect()->route('committee.events.show', $event->eventID)
                    ->with('error', 'Cannot edit a returned equipment borrowing.');
            }
            
            $equipment = $borrowing->equipment;
            
            return view('equipment-borrowings.edit', compact('event', 'borrowing', 'equipment'));
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->route('committee.events.show', $event->eventID)
                ->with('error', 'Failed to load edit form.');
        }
    }

    /**
     * Update the specified borrowing.
     */
    public function update(Request $request, Event $event, EquipmentBorrowing $borrowing)
    {
        try {
            // Verify the borrowing belongs to this event
            if ($borrowing->event_id != $event->eventID) {
                return redirect()->route('committee.events.show', $event->eventID)
                    ->with('error', 'Borrowing not found for this event.');
            }
            
            if ($borrowing->isReturned()) {
                return redirect()->route('committee.events.show', $event->eventID)
                    ->with('error', 'Cannot update a returned equipment borrowing.');
            }
            
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);
            
            $this->borrowingService->updateBorrowing($event->eventID, $borrowing->id, $validated['quantity']);
            
            return redirect()->route('committee.events.show', $event->eventID)
                ->with('success', 'Equipment borrowing updated successfully.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error updating borrowing: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update equipment borrowing. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified borrowing (manual return).
     */
    public function destroy(Event $event, EquipmentBorrowing $borrowing)
    {
        try {
            // Verify the borrowing belongs to this event
            if ($borrowing->event_id != $event->eventID) {
                return redirect()->route('committee.events.show', $event->eventID)
                    ->with('error', 'Borrowing not found for this event.');
            }
            
            $this->borrowingService->returnBorrowing($event->eventID, $borrowing->id);
            
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

