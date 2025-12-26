<?php

namespace App\Patterns\Decorator;

use App\Models\EquipmentBorrowing;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Decorator Pattern - Decorator for automatic return scheduling
 * Adds automatic return functionality to equipment borrowings
 */
class ReturnSchedulerDecorator
{
    protected EquipmentBorrowing $borrowing;

    public function __construct(EquipmentBorrowing $borrowing)
    {
        $this->borrowing = $borrowing;
    }

    /**
     * Get the decorated borrowing
     */
    public function getBorrowing(): EquipmentBorrowing
    {
        return $this->borrowing;
    }

    /**
     * Check if the borrowing should be returned based on event end time
     */
    public function shouldBeReturned(): bool
    {
        if ($this->borrowing->isReturned()) {
            return false;
        }

        return $this->borrowing->event->hasEnded();
    }

    /**
     * Process automatic return
     */
    public function processAutomaticReturn(): bool
    {
        if (!$this->shouldBeReturned()) {
            return false;
        }

        DB::beginTransaction();
        
        try {
            $equipment = $this->borrowing->equipment;
            $quantity = $this->borrowing->quantity;
            
            // Return equipment quantity
            $equipment->available_quantity += $quantity;
            $equipment->save();
            
            // Update borrowing status
            $this->borrowing->status = 'returned';
            $this->borrowing->returned_at = Carbon::now();
            $this->borrowing->save();
            
            // Log the return transaction
            $equipment->transactions()->create([
                'transaction_type' => 'event_return',
                'user_id' => $this->borrowing->user_id,
                'quantity' => $quantity,
                'transaction_date' => Carbon::now(),
                'notes' => "Automatically returned after event: {$this->borrowing->event->event_name} ended",
            ]);
            
            DB::commit();
            
            Log::info("Automatically returned equipment {$equipment->name} (quantity: {$quantity}) after event {$this->borrowing->event->event_name} ended");
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process automatic return: ' . $e->getMessage());
            throw $e;
        }
    }
}

