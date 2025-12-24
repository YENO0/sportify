<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EquipmentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EquipmentApiController extends Controller
{
    protected $equipmentService;

    public function __construct(EquipmentService $equipmentService)
    {
        $this->equipmentService = $equipmentService;
    }

    /**
     * Display a listing of equipment
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'status', 'sport_type_id', 'low_stock', 'sort', 'direction']);
            $perPage = $request->get('per_page', 10);
            
            $equipment = $this->equipmentService->getAll($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $equipment->items(),
                'meta' => [
                    'current_page' => $equipment->currentPage(),
                    'last_page' => $equipment->lastPage(),
                    'per_page' => $equipment->perPage(),
                    'total' => $equipment->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('EquipmentApiController::index - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve equipment list.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created equipment
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'sport_type_id' => 'required|exists:sport_types,id',
                'brand_id' => 'nullable|exists:brands,id',
                'model' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:1',
                'available_quantity' => 'nullable|integer|min:0',
                'minimum_stock_amount' => 'nullable|integer|min:0',
                'price' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:available,maintenance,retired',
                'location' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $equipment = $this->equipmentService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Equipment created successfully.',
                'data' => $equipment,
            ], 201);
        } catch (\Exception $e) {
            Log::error('EquipmentApiController::store - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create equipment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified equipment
     */
    public function show(int $id): JsonResponse
    {
        try {
            $equipment = $this->equipmentService->getById($id);

            return response()->json([
                'success' => true,
                'data' => $equipment,
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve equipment.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('EquipmentApiController::show - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve equipment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified equipment
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'sport_type_id' => 'sometimes|required|exists:sport_types,id',
                'brand_id' => 'nullable|exists:brands,id',
                'model' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'sometimes|required|integer|min:1',
                'available_quantity' => 'nullable|integer|min:0',
                'minimum_stock_amount' => 'nullable|integer|min:0',
                'price' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:available,maintenance,retired',
                'location' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $equipment = $this->equipmentService->update($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Equipment updated successfully.',
                'data' => $equipment,
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to update equipment.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('EquipmentApiController::update - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update equipment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified equipment
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->equipmentService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Equipment deleted successfully.',
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete equipment.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('EquipmentApiController::destroy - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete equipment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Checkout equipment
     */
    public function checkout(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $transaction = $this->equipmentService->checkout($id, $data['quantity'], $data['notes'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Equipment checked out successfully.',
                'data' => $transaction,
            ]);
        } catch (\App\Exceptions\EquipmentNotFoundException | \App\Exceptions\EquipmentStatusException | \App\Exceptions\InsufficientQuantityException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('EquipmentApiController::checkout - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to checkout equipment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return equipment
     */
    public function return(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $transaction = $this->equipmentService->return($id, $data['quantity'], $data['notes'] ?? null);

            return response()->json([
                'success' => true,
                'message' => 'Equipment returned successfully.',
                'data' => $transaction,
            ]);
        } catch (\App\Exceptions\EquipmentNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            Log::error('EquipmentApiController::return - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to return equipment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

