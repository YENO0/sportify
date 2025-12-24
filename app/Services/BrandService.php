<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BrandService
{
    /**
     * Get all brands with filters
     */
    public function getAll(array $filters = [], int $perPage = 15)
    {
        try {
            $query = Brand::withCount('equipment');

            // Search functionality
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
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
            Log::error('BrandService::getAll - Error: ' . $e->getMessage(), [
                'filters' => $filters,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve brands: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get brand by ID
     */
    public function getById(int $id): Brand
    {
        try {
            $brand = Brand::withCount('equipment')->find($id);
            
            if (!$brand) {
                throw new \RuntimeException("Brand with ID {$id} not found.", 404);
            }
            
            return $brand;
        } catch (\Exception $e) {
            Log::error('BrandService::getById - Error: ' . $e->getMessage(), [
                'brand_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to retrieve brand: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create new brand
     */
    public function create(array $data): Brand
    {
        DB::beginTransaction();
        try {
            $brand = Brand::create($data);
            
            DB::commit();
            Log::info('Brand created successfully', ['brand_id' => $brand->id]);
            
            return $brand;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BrandService::create - Error: ' . $e->getMessage(), [
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to create brand: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update brand
     */
    public function update(int $id, array $data): Brand
    {
        DB::beginTransaction();
        try {
            $brand = $this->getById($id);
            $brand->update($data);
            
            DB::commit();
            Log::info('Brand updated successfully', ['brand_id' => $brand->id]);
            
            return $brand->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BrandService::update - Error: ' . $e->getMessage(), [
                'brand_id' => $id,
                'data' => $data,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to update brand: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Delete brand
     */
    public function delete(int $id): bool
    {
        DB::beginTransaction();
        try {
            $brand = $this->getById($id);
            
            // Check if brand has equipment
            if ($brand->equipment()->count() > 0) {
                throw new \InvalidArgumentException('Cannot delete brand that has associated equipment.');
            }
            
            $brand->delete();
            
            DB::commit();
            Log::info('Brand deleted successfully', ['brand_id' => $id]);
            
            return true;
        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('BrandService::delete - Error: ' . $e->getMessage(), [
                'brand_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \RuntimeException('Failed to delete brand: ' . $e->getMessage(), 0, $e);
        }
    }
}

