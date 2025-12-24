<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MaintenanceApiController extends Controller
{
    protected $maintenanceService;

    public function __construct(MaintenanceService $maintenanceService)
    {
        $this->maintenanceService = $maintenanceService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'status', 'equipment_id', 'sort', 'direction']);
            $perPage = $request->get('per_page', 15);
            
            $maintenances = $this->maintenanceService->getAll($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $maintenances->items(),
                'meta' => [
                    'current_page' => $maintenances->currentPage(),
                    'last_page' => $maintenances->lastPage(),
                    'per_page' => $maintenances->perPage(),
                    'total' => $maintenances->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('MaintenanceApiController::index - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve maintenances.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'equipment_id' => 'required|exists:equipment,id',
                'maintenance_type' => 'required|in:preventive,repair,emergency,scheduled',
                'description' => 'required|string',
                'scheduled_date' => 'nullable|date',
                'assigned_user_id' => 'nullable|exists:users,id',
                'quantity' => 'nullable|integer|min:1',
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
            $maintenance = $this->maintenanceService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Maintenance created successfully.',
                'data' => $maintenance,
            ], 201);
        } catch (\Exception $e) {
            Log::error('MaintenanceApiController::store - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create maintenance.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $maintenance = $this->maintenanceService->getById($id);

            return response()->json([
                'success' => true,
                'data' => $maintenance,
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maintenance not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve maintenance.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('MaintenanceApiController::show - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve maintenance.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed,cancelled',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $status = $request->input('status');
            $maintenance = $this->maintenanceService->updateStatus($id, $status);

            return response()->json([
                'success' => true,
                'message' => 'Maintenance status updated successfully.',
                'data' => $maintenance,
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maintenance not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to update maintenance status.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('MaintenanceApiController::updateStatus - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update maintenance status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUpcoming(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['equipment_id']);
            $days = $request->get('days', 30);
            
            $maintenances = $this->maintenanceService->getUpcoming($filters, $days);

            return response()->json([
                'success' => true,
                'data' => $maintenances,
            ]);
        } catch (\Exception $e) {
            Log::error('MaintenanceApiController::getUpcoming - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve upcoming maintenances.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

