<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EquipmentBorrowing;
use App\Patterns\Decorator\ReturnSchedulerDecorator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EventService
{
    /**
     * Get all events with filters
     */
    public function getAll(array $filters = [], int $perPage = 15)
    {
        try {
            $query = Event::with(['equipmentBorrowings.equipment']);

            // Search
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }
            
            // Filter by status
            if (!empty($filters['status'])) {
                if ($filters['status'] === 'past') {
                    $query->where('end_date', '<', now());
                } elseif ($filters['status'] === 'upcoming') {
                    $query->where('start_date', '>', now());
                } elseif ($filters['status'] === 'active') {
                    $query->where('start_date', '<=', now())
                          ->where('end_date', '>=', now());
                }
            }

            // Sorting
            $sortColumn = $filters['sort'] ?? 'start_date';
            $sortDirection = $filters['direction'] ?? 'desc';
            
            $allowedSortColumns = ['name', 'start_date', 'end_date'];
            if (!in_array($sortColumn, $allowedSortColumns)) {
                $sortColumn = 'start_date';
            }
            
            $query->orderBy($sortColumn, $sortDirection);

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('EventService::getAll - Error: ' . $e->getMessage(), [
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve events: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get event by ID
     */
    public function getById(int $id): Event
    {
        try {
            $event = Event::with(['equipmentBorrowings.equipment.brand', 'equipmentBorrowings.user'])
                ->find($id);
            
            if (!$event) {
                throw new \RuntimeException("Event with ID {$id} not found.", 404);
            }
            
            return $event;
        } catch (\Exception $e) {
            Log::error('EventService::getById - Error: ' . $e->getMessage(), [
                'event_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve event: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create new event
     */
    public function create(array $data): Event
    {
        DB::beginTransaction();
        try {
            // Combine date and time
            $startDateTime = Carbon::parse($data['start_date'] . ' ' . $data['start_time']);
            $endDateTime = Carbon::parse($data['end_date'] . ' ' . $data['end_time']);

            if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                throw new \InvalidArgumentException('The end date and time must be after the start date and time.');
            }

            $event = Event::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
            ]);
            
            DB::commit();
            Log::info('Event created successfully', ['event_id' => $event->id]);
            
            return $event->fresh();
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EventService::create - Error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to create event: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update event
     */
    public function update(int $id, array $data): Event
    {
        DB::beginTransaction();
        try {
            $event = $this->getById($id);
            
            // Combine date and time
            $startDateTime = Carbon::parse($data['start_date'] . ' ' . $data['start_time']);
            $endDateTime = Carbon::parse($data['end_date'] . ' ' . $data['end_time']);

            if ($endDateTime->lessThanOrEqualTo($startDateTime)) {
                throw new \InvalidArgumentException('The end date and time must be after the start date and time.');
            }

            $event->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'start_date' => $startDateTime,
                'end_date' => $endDateTime,
            ]);
            
            DB::commit();
            Log::info('Event updated successfully', ['event_id' => $event->id]);
            
            return $event->fresh();
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EventService::update - Error: ' . $e->getMessage(), [
                'event_id' => $id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to update event: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete event
     */
    public function delete(int $id): bool
    {
        DB::beginTransaction();
        try {
            $event = $this->getById($id);
            
            // Return all borrowed equipment associated with this event
            foreach ($event->equipmentBorrowings()->where('status', 'borrowed')->get() as $borrowing) {
                $decorator = new ReturnSchedulerDecorator($borrowing);
                $decorator->processAutomaticReturn();
            }
            
            $event->delete();
            
            DB::commit();
            Log::info('Event deleted successfully', ['event_id' => $id]);
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EventService::delete - Error: ' . $e->getMessage(), [
                'event_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to delete event: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Test return logic by setting event end date to past
     */
    public function testReturnLogic(int $id): array
    {
        DB::beginTransaction();
        try {
            $event = $this->getById($id);
            
            // Set event end date/time to a past value
            $event->update([
                'end_date' => now()->subDay(),
            ]);

            // Process returns for this event's borrowings
            $borrowings = $event->equipmentBorrowings()->where('status', 'borrowed')->get();
            $returnedCount = 0;
            
            foreach ($borrowings as $borrowing) {
                $decorator = new ReturnSchedulerDecorator($borrowing);
                if ($decorator->processAutomaticReturn()) {
                    $returnedCount++;
                }
            }
            
            DB::commit();
            Log::info('Test return logic executed', [
                'event_id' => $id,
                'returned_count' => $returnedCount
            ]);
            
            return [
                'success' => true,
                'returned_count' => $returnedCount,
                'message' => "Test return logic executed. {$returnedCount} equipment items returned."
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EventService::testReturnLogic - Error: ' . $e->getMessage(), [
                'event_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to execute test return logic: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Process automatic returns for past events
     */
    public function processAutomaticReturns(): array
    {
        try {
            $events = Event::where('end_date', '<', now())
                ->with(['equipmentBorrowings' => function($query) {
                    $query->where('status', 'borrowed');
                }])
                ->get();
            
            $totalReturned = 0;
            
            foreach ($events as $event) {
                foreach ($event->equipmentBorrowings as $borrowing) {
                    $decorator = new ReturnSchedulerDecorator($borrowing);
                    if ($decorator->processAutomaticReturn()) {
                        $totalReturned++;
                    }
                }
            }
            
            Log::info('Automatic returns processed', ['total_returned' => $totalReturned]);
            
            return [
                'success' => true,
                'total_returned' => $totalReturned,
                'message' => "Processed automatic returns. {$totalReturned} equipment items returned."
            ];
        } catch (\Exception $e) {
            Log::error('EventService::processAutomaticReturns - Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to process automatic returns: ' . $e->getMessage(), 0, $e);
        }
    }
}

