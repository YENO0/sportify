<?php

namespace App\Services;

use App\Models\SportType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SportTypeService
{
    /**
     * Get all sport types with filters
     */
    public function getAll(array $filters = [], int $perPage = 15)
    {
        try {
            $query = SportType::withCount('equipment');

            // Search functionality
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            // Filter by active status
            if (isset($filters['is_active']) && $filters['is_active'] !== '') {
                $isActive = $filters['is_active'] == '1' || $filters['is_active'] === 1 || $filters['is_active'] === true;
                $query->where('is_active', $isActive);
            }

            // Sorting
            $sortColumn = $filters['sort'] ?? 'name';
            $sortDirection = $filters['direction'] ?? 'asc';
            
            // Validate sort column
            $allowedSortColumns = ['name', 'created_at', 'equipment_count'];
            if (!in_array($sortColumn, $allowedSortColumns)) {
                $sortColumn = 'name';
            }
            
            if ($sortColumn === 'equipment_count') {
                $query->orderBy('equipment_count', $sortDirection);
            } else {
                $query->orderBy($sortColumn, $sortDirection);
            }

            return $query->paginate($perPage);
        } catch (\Exception $e) {
            Log::error('SportTypeService::getAll - Error: ' . $e->getMessage(), [
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve sport types: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get sport type by ID
     */
    public function getById(int $id): SportType
    {
        try {
            $sportType = SportType::withCount('equipment')->find($id);
            
            if (!$sportType) {
                throw new \RuntimeException("Sport type with ID {$id} not found.", 404);
            }
            
            return $sportType;
        } catch (\Exception $e) {
            Log::error('SportTypeService::getById - Error: ' . $e->getMessage(), [
                'sport_type_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve sport type: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create new sport type
     */
    public function create(array $data): SportType
    {
        DB::beginTransaction();
        try {
            // Generate slug if not provided
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            $sportType = SportType::create($data);
            
            DB::commit();
            Log::info('Sport type created successfully', ['sport_type_id' => $sportType->id]);
            
            return $sportType;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SportTypeService::create - Error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to create sport type: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update sport type
     */
    public function update(int $id, array $data): SportType
    {
        DB::beginTransaction();
        try {
            $sportType = $this->getById($id);
            
            // Generate slug if name changed and slug not provided
            if (isset($data['name']) && $data['name'] !== $sportType->name && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            
            $sportType->update($data);
            
            DB::commit();
            Log::info('Sport type updated successfully', ['sport_type_id' => $sportType->id]);
            
            return $sportType->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SportTypeService::update - Error: ' . $e->getMessage(), [
                'sport_type_id' => $id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to update sport type: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete sport type
     */
    public function delete(int $id): bool
    {
        DB::beginTransaction();
        try {
            $sportType = $this->getById($id);
            
            // Check if sport type has equipment
            if ($sportType->equipment()->count() > 0) {
                throw new \InvalidArgumentException('Cannot delete sport type that has associated equipment.');
            }
            
            $sportType->delete();
            
            DB::commit();
            Log::info('Sport type deleted successfully', ['sport_type_id' => $id]);
            
            return true;
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('SportTypeService::delete - Error: ' . $e->getMessage(), [
                'sport_type_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to delete sport type: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get active sport types
     */
    public function getActive()
    {
        try {
            return SportType::where('is_active', true)
                ->orderBy('name', 'asc')
                ->get();
        } catch (\Exception $e) {
            Log::error('SportTypeService::getActive - Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve active sport types: ' . $e->getMessage(), 0, $e);
        }
    }
}

