<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Equipment;
use App\Models\EquipmentBorrowing;
use App\Patterns\Factory\BorrowingFactoryManager;
use App\Patterns\Decorator\BorrowingDecoratorManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EquipmentBorrowingService
{
    /**
     * Get available equipment for borrowing
     */
    public function getAvailableEquipment(): \Illuminate\Database\Eloquent\Collection
    {
        try {
            return Equipment::where('status', 'available')
                ->where('available_quantity', '>', 0)
                ->with(['brand', 'sportType'])
                ->orderBy('name', 'asc')
                ->get();
        } catch (\Exception $e) {
            Log::error('EquipmentBorrowingService::getAvailableEquipment - Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve available equipment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get borrowed equipment IDs for an event
     */
    public function getBorrowedEquipmentIds(int $eventId): array
    {
        try {
            return Event::findOrFail($eventId)
                ->equipmentBorrowings()
                ->pluck('equipment_id')
                ->toArray();
        } catch (\Exception $e) {
            Log::error('EquipmentBorrowingService::getBorrowedEquipmentIds - Error: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve borrowed equipment IDs: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create equipment borrowings for an event
     */
    public function createBorrowings(int $eventId, array $borrowingsData): array
    {
        DB::beginTransaction();
        try {
            $event = Event::findOrFail($eventId);
            
            // Validate arrays have same length
            if (count($borrowingsData['equipment']) !== count($borrowingsData['quantity'])) {
                throw new \InvalidArgumentException('Equipment and quantity arrays must have the same length.');
            }

            // Check for duplicate equipment IDs
            if (count($borrowingsData['equipment']) !== count(array_unique($borrowingsData['equipment']))) {
                throw new \InvalidArgumentException('Duplicate equipment selections are not allowed.');
            }

            $createdBorrowings = [];
            $successMessages = [];
            
            foreach ($borrowingsData['equipment'] as $index => $equipmentId) {
                $equipment = Equipment::findOrFail($equipmentId);
                $quantity = $borrowingsData['quantity'][$index];
                
                // Validate quantity doesn't exceed available
                if ($equipment->available_quantity < $quantity) {
                    throw new \InvalidArgumentException(
                        "Insufficient quantity for {$equipment->name}. Available: {$equipment->available_quantity}, Requested: {$quantity}"
                    );
                }
                
                // Check if equipment is already borrowed for this event
                $existingBorrowing = $event->equipmentBorrowings()
                    ->where('equipment_id', $equipmentId)
                    ->where('status', 'borrowed')
                    ->first();
                
                if ($existingBorrowing) {
                    throw new \InvalidArgumentException("{$equipment->name} is already borrowed for this event.");
                }
                
                // Use Factory Method pattern to create borrowing
                $borrowing = BorrowingFactoryManager::create($equipment, $event, [
                    'quantity' => $quantity,
                    'notes' => $borrowingsData['notes'] ?? null,
                ]);
                
                // Use Decorator to schedule return
                $decoratorManager = new BorrowingDecoratorManager($borrowing);
                $returnScheduler = $decoratorManager->withReturnScheduler();
                $returnScheduler->scheduleReturn();
                $returnScheduler->processReturn(); // Process if already past
                
                $createdBorrowings[] = $borrowing;
                $successMessages[] = "{$quantity} {$equipment->name}(s)";
            }
            
            DB::commit();
            Log::info('Equipment borrowings created successfully', [
                'event_id' => $eventId,
                'borrowings_count' => count($createdBorrowings)
            ]);
            
            return [
                'borrowings' => $createdBorrowings,
                'message' => "Successfully borrowed: " . implode(', ', $successMessages) . " for event."
            ];
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EquipmentBorrowingService::createBorrowings - Error: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'data' => $borrowingsData,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to create equipment borrowings: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete/Return a borrowing (manual return)
     */
    public function returnBorrowing(int $eventId, int $borrowingId): bool
    {
        DB::beginTransaction();
        try {
            $event = Event::findOrFail($eventId);
            $borrowing = $event->equipmentBorrowings()->findOrFail($borrowingId);
            
            if ($borrowing->isReturned()) {
                throw new \InvalidArgumentException('Equipment has already been returned.');
            }
            
            // Return equipment quantity
            $equipment = $borrowing->equipment;
            $equipment->available_quantity += $borrowing->quantity;
            $equipment->save();
            
            // Update borrowing status
            $borrowing->status = 'returned';
            $borrowing->returned_at = now();
            $borrowing->save();
            
            // Log the return transaction
            $equipment->transactions()->create([
                'transaction_type' => 'event_return_manual',
                'user_id' => auth()->id(),
                'quantity' => $borrowing->quantity,
                'transaction_date' => now(),
                'notes' => "Manually returned from event: {$event->name}",
            ]);
            
            DB::commit();
            Log::info('Equipment borrowing returned successfully', [
                'borrowing_id' => $borrowingId,
                'event_id' => $eventId
            ]);
            
            return true;
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EquipmentBorrowingService::returnBorrowing - Error: ' . $e->getMessage(), [
                'borrowing_id' => $borrowingId,
                'event_id' => $eventId,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to return equipment borrowing: ' . $e->getMessage(), 0, $e);
        }
    }
}

