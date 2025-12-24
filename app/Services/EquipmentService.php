<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\EquipmentTransaction;
use App\Patterns\Factory\EquipmentFactoryManager;
use App\Patterns\Decorator\EquipmentDecoratorManager;
use App\Exceptions\EquipmentNotFoundException;
use App\Exceptions\InsufficientQuantityException;
use App\Exceptions\EquipmentStatusException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class EquipmentService
{
    /**
     * Get all equipment with filters
     */
    public function getAll(array $filters = [], int $perPage = 10)
    {
        try {
            $query = Equipment::with(['features', 'brand', 'sportType']);

            // Search functionality
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%")
                      ->orWhereHas('brand', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('sportType', function($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            // Filter by sport type
            if (!empty($filters['sport_type_id'])) {
                $query->where('sport_type_id', $filters['sport_type_id']);
            }

            // Filter by low stock
            if (isset($filters['low_stock']) && $filters['low_stock'] == '1') {
                $query->whereColumn('available_quantity', '<', 'minimum_stock_amount');
            }

            // Sorting
            $sortColumn = $filters['sort'] ?? 'created_at';
            $sortDirection = $filters['direction'] ?? 'desc';
            
            // Validate sort column to prevent SQL injection
            $allowedSortColumns = ['name', 'status', 'quantity', 'available_quantity', 'location', 'created_at', 'sport_type_id'];
            if (!in_array($sortColumn, $allowedSortColumns)) {
                $sortColumn = 'created_at';
            }
            
            if ($sortColumn === 'sport_type_id') {
                $query->join('sport_types', 'equipment.sport_type_id', '=', 'sport_types.id')
                      ->select('equipment.*')
                      ->orderBy('sport_types.name', $sortDirection);
            } else {
                $query->orderBy($sortColumn, $sortDirection);
            }

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('EquipmentService::getAll - Error: ' . $e->getMessage(), [
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve equipment list: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get equipment by ID
     */
    public function getById(int $id): Equipment
    {
        try {
            $equipment = Equipment::with(['features', 'brand', 'sportType', 'images', 'transactions'])
                ->find($id);
            
            if (!$equipment) {
                throw new EquipmentNotFoundException("Equipment with ID {$id} not found.", $id);
            }
            
            return $equipment;
        } catch (EquipmentNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('EquipmentService::getById - Error: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve equipment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create new equipment
     */
    public function create(array $data): Equipment
    {
        DB::beginTransaction();
        try {
            // Use Factory Method pattern
            $equipment = EquipmentFactoryManager::create($data);
            
            // Apply decorators if needed
            if (isset($data['images']) && !empty($data['images'])) {
                $decoratorManager = new EquipmentDecoratorManager($equipment);
                $imageDecorator = $decoratorManager->withImageDecorator();
                $imageDecorator->addImages($data['images']);
            }
            
            DB::commit();
            Log::info('Equipment created successfully', ['equipment_id' => $equipment->id]);
            
            return $equipment->fresh(['brand', 'sportType', 'features', 'images']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EquipmentService::create - Error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to create equipment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update equipment
     */
    public function update(int $id, array $data): Equipment
    {
        DB::beginTransaction();
        try {
            $equipment = $this->getById($id);
            
            $equipment->update($data);
            
            // Handle images if provided
            if (isset($data['images']) && !empty($data['images'])) {
                $decoratorManager = new EquipmentDecoratorManager($equipment);
                $imageDecorator = $decoratorManager->withImageDecorator();
                $imageDecorator->addImages($data['images']);
            }
            
            DB::commit();
            Log::info('Equipment updated successfully', ['equipment_id' => $equipment->id]);
            
            return $equipment->fresh(['brand', 'sportType', 'features', 'images']);
        } catch (EquipmentNotFoundException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EquipmentService::update - Error: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to update equipment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete equipment
     */
    public function delete(int $id): bool
    {
        DB::beginTransaction();
        try {
            $equipment = $this->getById($id);
            $equipment->delete();
            
            DB::commit();
            Log::info('Equipment deleted successfully', ['equipment_id' => $id]);
            
            return true;
        } catch (EquipmentNotFoundException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EquipmentService::delete - Error: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to delete equipment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Checkout equipment
     */
    public function checkout(int $id, int $quantity, ?string $notes = null): EquipmentTransaction
    {
        DB::beginTransaction();
        try {
            $equipment = $this->getById($id);
            
            if ($equipment->status !== 'available') {
                throw new EquipmentStatusException(
                    "Equipment is not available for checkout. Current status: {$equipment->status}",
                    $equipment->status,
                    'available'
                );
            }
            
            if ($equipment->available_quantity < $quantity) {
                throw new InsufficientQuantityException(
                    "Insufficient quantity. Available: {$equipment->available_quantity}, Requested: {$quantity}",
                    $quantity,
                    $equipment->available_quantity
                );
            }
            
            $equipment->available_quantity -= $quantity;
            $equipment->save();
            
            $transaction = EquipmentTransaction::create([
                'equipment_id' => $equipment->id,
                'transaction_type' => 'checkout',
                'user_id' => auth()->id(),
                'quantity' => $quantity,
                'notes' => $notes,
                'transaction_date' => now(),
            ]);
            
            DB::commit();
            Log::info('Equipment checked out successfully', [
                'equipment_id' => $id,
                'quantity' => $quantity,
                'transaction_id' => $transaction->id
            ]);
            
            return $transaction;
        } catch (EquipmentNotFoundException | EquipmentStatusException | InsufficientQuantityException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EquipmentService::checkout - Error: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'quantity' => $quantity,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to checkout equipment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Return equipment
     */
    public function return(int $id, int $quantity, ?string $notes = null): EquipmentTransaction
    {
        DB::beginTransaction();
        try {
            $equipment = $this->getById($id);
            
            $equipment->available_quantity += $quantity;
            
            // Ensure available quantity doesn't exceed total quantity
            if ($equipment->available_quantity > $equipment->quantity) {
                $equipment->available_quantity = $equipment->quantity;
            }
            
            $equipment->save();
            
            $transaction = EquipmentTransaction::create([
                'equipment_id' => $equipment->id,
                'transaction_type' => 'return',
                'user_id' => auth()->id(),
                'quantity' => $quantity,
                'notes' => $notes,
                'transaction_date' => now(),
            ]);
            
            DB::commit();
            Log::info('Equipment returned successfully', [
                'equipment_id' => $id,
                'quantity' => $quantity,
                'transaction_id' => $transaction->id
            ]);
            
            return $transaction;
        } catch (EquipmentNotFoundException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('EquipmentService::return - Error: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'quantity' => $quantity,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to return equipment: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): array
    {
        try {
            $totalEquipment = Equipment::count();
            $availableEquipment = Equipment::where('status', 'available')->count();
            $maintenanceEquipment = Equipment::where('status', 'maintenance')->count();
            $damagedEquipment = Equipment::where('status', 'damaged')->count();
            
            $totalValue = Equipment::sum(DB::raw('price * quantity'));
            $utilizationRate = $totalEquipment > 0 
                ? Equipment::selectRaw('AVG((quantity - available_quantity) / quantity * 100) as rate')
                    ->value('rate') ?? 0
                : 0;

            // Count low stock items
            $lowStockCount = Equipment::whereColumn('available_quantity', '<', 'minimum_stock_amount')
                ->where('status', '!=', 'retired')
                ->count();

            return [
                'total_equipment' => $totalEquipment,
                'available_equipment' => $availableEquipment,
                'maintenance_equipment' => $maintenanceEquipment,
                'damaged_equipment' => $damagedEquipment,
                'total_value' => $totalValue,
                'utilization_rate' => round($utilizationRate, 2),
                'low_stock_count' => $lowStockCount,
            ];
        } catch (\Exception $e) {
            Log::error('EquipmentService::getDashboardStats - Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'total_equipment' => 0,
                'available_equipment' => 0,
                'maintenance_equipment' => 0,
                'damaged_equipment' => 0,
                'total_value' => 0,
                'utilization_rate' => 0,
                'low_stock_count' => 0,
            ];
        }
    }

    /**
     * Get low stock equipment
     */
    public function getLowStockEquipment()
    {
        try {
            return Equipment::whereColumn('available_quantity', '<', 'minimum_stock_amount')
                ->where('status', '!=', 'retired')
                ->with(['brand', 'sportType'])
                ->orderByRaw('(available_quantity - minimum_stock_amount) ASC')
                ->get();
        } catch (\Exception $e) {
            Log::error('EquipmentService::getLowStockEquipment - Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve low stock equipment: ' . $e->getMessage(), 0, $e);
        }
    }
}

