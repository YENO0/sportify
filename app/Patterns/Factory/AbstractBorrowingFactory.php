<?php

namespace App\Patterns\Factory;

use App\Models\Equipment;
use App\Models\Event;
use App\Models\EquipmentBorrowing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Factory Method Pattern - Abstract base factory for equipment borrowings
 * Implements template method pattern for borrowing creation
 */
abstract class AbstractBorrowingFactory implements BorrowingFactoryInterface
{
    /**
     * Template method for creating borrowing
     * Defines the algorithm structure
     */
    public function createBorrowing(Equipment $equipment, Event $event, array $data)
    {
        DB::beginTransaction();
        
        try {
            // Step 1: Validate borrowing
            $this->validateBorrowing($equipment, $event, $data);
            
            // Step 2: Prepare borrowing data (Factory Method)
            $borrowingData = $this->prepareBorrowingData($equipment, $event, $data);
            
            // Step 3: Deduct equipment quantity
            $this->deductEquipmentQuantity($equipment, $borrowingData['quantity']);
            
            // Step 4: Create borrowing record
            $borrowing = $this->createBorrowingRecord($borrowingData);
            
            // Step 5: Post-creation logic (Factory Method)
            $this->postCreation($borrowing, $equipment, $event);
            
            DB::commit();
            
            return $borrowing;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create borrowing: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate the borrowing request
     */
    protected function validateBorrowing(Equipment $equipment, Event $event, array $data): void
    {
        $quantity = $data['quantity'] ?? 0;
        
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than 0.');
        }
        
        if ($equipment->available_quantity < $quantity) {
            throw new \InvalidArgumentException(
                "Insufficient quantity. Available: {$equipment->available_quantity}, Requested: {$quantity}"
            );
        }
        
        if ($equipment->status !== 'available') {
            throw new \InvalidArgumentException("Equipment is not available for borrowing. Status: {$equipment->status}");
        }
    }

    /**
     * Prepare borrowing data (Factory Method - to be implemented by subclasses)
     */
    abstract protected function prepareBorrowingData(Equipment $equipment, Event $event, array $data): array;

    /**
     * Deduct equipment quantity
     */
    protected function deductEquipmentQuantity(Equipment $equipment, int $quantity): void
    {
        $equipment->available_quantity -= $quantity;
        $equipment->save();
    }

    /**
     * Create borrowing record
     */
    protected function createBorrowingRecord(array $data): EquipmentBorrowing
    {
        return EquipmentBorrowing::create($data);
    }

    /**
     * Post-creation logic (Factory Method - to be implemented by subclasses)
     */
    abstract protected function postCreation(EquipmentBorrowing $borrowing, Equipment $equipment, Event $event): void;
}

