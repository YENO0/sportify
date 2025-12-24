<?php

namespace App\Services;

use App\Models\Maintenance;
use App\Models\Equipment;
use App\Patterns\Factory\MaintenanceFactoryManager;
use App\Patterns\Decorator\MaintenanceQuantityDecorator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaintenanceService
{
    /**
     * Get all maintenances with filters
     */
    public function getAll(array $filters = [], int $perPage = 15)
    {
        try {
            // Process completed maintenances and return quantities
            $this->processCompletedMaintenances();

            $query = Maintenance::with(['equipment.brand', 'equipment.sportType', 'assignedUser']);

            // Search functionality
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhere('notes', 'like', "%{$search}%")
                      ->orWhereHas('equipment', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            // Filter by equipment
            if (!empty($filters['equipment_id'])) {
                $query->where('equipment_id', $filters['equipment_id']);
            }

            // Sorting
            $sortColumn = $filters['sort'] ?? 'created_at';
            $sortDirection = $filters['direction'] ?? 'desc';
            
            // Validate sort column
            $allowedSortColumns = ['created_at', 'scheduled_date', 'start_date', 'end_date', 'status'];
            if (!in_array($sortColumn, $allowedSortColumns)) {
                $sortColumn = 'created_at';
            }
            
            if ($sortColumn === 'scheduled_date' || $sortColumn === 'start_date' || $sortColumn === 'end_date') {
                $query->orderByRaw("COALESCE({$sortColumn}, created_at) {$sortDirection}");
            } else {
                $query->orderBy($sortColumn, $sortDirection);
            }

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('MaintenanceService::getAll - Error: ' . $e->getMessage(), [
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve maintenances: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get upcoming maintenances
     */
    public function getUpcoming(array $filters = [], int $days = 30)
    {
        try {
            $query = Maintenance::with(['equipment.brand', 'equipment.sportType', 'assignedUser'])
                ->where('status', 'pending')
                ->where(function($q) {
                    $q->where('start_date', '>=', now())
                      ->orWhere('scheduled_date', '>=', now());
                })
                ->where(function($q) use ($days) {
                    $q->where('start_date', '<=', now()->addDays($days))
                      ->orWhere('scheduled_date', '<=', now()->addDays($days));
                });

            // Apply filters
            if (!empty($filters['equipment_id'])) {
                $query->where('equipment_id', $filters['equipment_id']);
            }

            return $query->get();
        } catch (\Exception $e) {
            Log::error('MaintenanceService::getUpcoming - Error: ' . $e->getMessage(), [
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve upcoming maintenances: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get maintenance by ID
     */
    public function getById(int $id): Maintenance
    {
        try {
            $maintenance = Maintenance::with(['equipment.brand', 'equipment.sportType', 'assignedUser'])
                ->find($id);
            
            if (!$maintenance) {
                throw new \RuntimeException("Maintenance with ID {$id} not found.", 404);
            }
            
            return $maintenance;
        } catch (\Exception $e) {
            Log::error('MaintenanceService::getById - Error: ' . $e->getMessage(), [
                'maintenance_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve maintenance: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create new maintenance
     */
    public function create(array $data): Maintenance
    {
        DB::beginTransaction();
        try {
            // Use Factory Method pattern
            $maintenance = MaintenanceFactoryManager::create($data);
            
            DB::commit();
            Log::info('Maintenance created successfully', ['maintenance_id' => $maintenance->id]);
            
            return $maintenance->fresh(['equipment', 'assignedUser']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MaintenanceService::create - Error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to create maintenance: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update maintenance status
     */
    public function updateStatus(int $id, string $status): Maintenance
    {
        DB::beginTransaction();
        try {
            $maintenance = $this->getById($id);
            
            $allowedStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];
            if (!in_array($status, $allowedStatuses)) {
                throw new \InvalidArgumentException("Invalid status: {$status}");
            }
            
            $maintenance->status = $status;
            
            if ($status === 'in_progress' && !$maintenance->start_date) {
                $maintenance->start_date = now();
            }
            
            if ($status === 'completed') {
                $maintenance->end_date = now();
                
                // Use Decorator to return equipment quantity
                $decorator = new MaintenanceQuantityDecorator($maintenance);
                $decorator->returnQuantity();
            }
            
            $maintenance->save();
            
            DB::commit();
            Log::info('Maintenance status updated successfully', [
                'maintenance_id' => $id,
                'status' => $status
            ]);
            
            return $maintenance->fresh(['equipment', 'assignedUser']);
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MaintenanceService::updateStatus - Error: ' . $e->getMessage(), [
                'maintenance_id' => $id,
                'status' => $status,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to update maintenance status: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Process completed maintenances and return quantities
     */
    protected function processCompletedMaintenances(): void
    {
        try {
            $completedMaintenances = Maintenance::where('status', 'completed')
                ->whereNull('end_date')
                ->orWhere(function($query) {
                    $query->where('status', 'completed')
                          ->where('end_date', '<=', now());
                })
                ->get();
            
            foreach ($completedMaintenances as $maintenance) {
                $decorator = new MaintenanceQuantityDecorator($maintenance);
                $decorator->returnQuantity();
            }
        } catch (\Exception $e) {
            Log::error('MaintenanceService::processCompletedMaintenances - Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw - this is a background process
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        try {
            return [
                'total_maintenances' => Maintenance::count(),
                'pending_maintenances' => Maintenance::where('status', 'pending')->count(),
                'in_progress_maintenances' => Maintenance::where('status', 'in_progress')->count(),
                'completed_maintenances' => Maintenance::where('status', 'completed')->count(),
                'upcoming_maintenances' => Maintenance::where('status', 'pending')
                    ->where(function($q) {
                        $q->where('start_date', '>=', now())
                          ->orWhere('scheduled_date', '>=', now());
                    })
                    ->where(function($q) {
                        $q->where('start_date', '<=', now()->addDays(30))
                          ->orWhere('scheduled_date', '<=', now()->addDays(30));
                    })
                    ->count(),
            ];
        } catch (\Exception $e) {
            Log::error('MaintenanceService::getDashboardStats - Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve dashboard statistics: ' . $e->getMessage(), 0, $e);
        }
    }
}

