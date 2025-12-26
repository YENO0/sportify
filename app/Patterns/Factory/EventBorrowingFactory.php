<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;
use App\Models\Event;
use App\Models\EquipmentBorrowing;
use Carbon\Carbon;

/**
 * Factory Method Pattern - Concrete factory for event-based equipment borrowings
 */
class EventBorrowingFactory extends AbstractBorrowingFactory
{
    /**
     * Prepare borrowing data for event-based borrowing
     */
    protected function prepareBorrowingData(Equipment $equipment, Event $event, array $data): array
    {
        return [
            'event_id' => $event->eventID,
            'equipment_id' => $equipment->id,
            'quantity' => $data['quantity'],
            'status' => 'borrowed',
            'borrowed_at' => Carbon::now(),
            'notes' => $data['notes'] ?? null,
            'user_id' => auth()->id(),
        ];
    }

    /**
     * Post-creation logic for event-based borrowing
     */
    protected function postCreation(EquipmentBorrowing $borrowing, Equipment $equipment, Event $event): void
    {
        // Log the borrowing transaction
        $equipment->transactions()->create([
            'transaction_type' => 'event_borrowing',
            'user_id' => auth()->id(),
            'quantity' => $borrowing->quantity,
            'transaction_date' => Carbon::now(),
            'notes' => "Borrowed for event: {$event->event_name}",
        ]);
    }
}

