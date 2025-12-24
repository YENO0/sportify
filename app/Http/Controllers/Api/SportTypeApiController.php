<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SportTypeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class SportTypeApiController extends Controller
{
    protected $sportTypeService;

    public function __construct(SportTypeService $sportTypeService)
    {
        $this->sportTypeService = $sportTypeService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'is_active', 'sort', 'direction']);
            $perPage = $request->get('per_page', 15);
            
            $sportTypes = $this->sportTypeService->getAll($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $sportTypes->items(),
                'meta' => [
                    'current_page' => $sportTypes->currentPage(),
                    'last_page' => $sportTypes->lastPage(),
                    'per_page' => $sportTypes->perPage(),
                    'total' => $sportTypes->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('SportTypeApiController::index - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sport types.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:sport_types,name',
                'description' => 'nullable|string',
                'slug' => 'nullable|string|max:255|unique:sport_types,slug',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $sportType = $this->sportTypeService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Sport type created successfully.',
                'data' => $sportType,
            ], 201);
        } catch (\Exception $e) {
            Log::error('SportTypeApiController::store - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sport type.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $sportType = $this->sportTypeService->getById($id);

            return response()->json([
                'success' => true,
                'data' => $sportType,
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sport type not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sport type.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('SportTypeApiController::show - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sport type.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255|unique:sport_types,name,' . $id,
                'description' => 'nullable|string',
                'slug' => 'nullable|string|max:255|unique:sport_types,slug,' . $id,
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $sportType = $this->sportTypeService->update($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Sport type updated successfully.',
                'data' => $sportType,
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sport type not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sport type.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('SportTypeApiController::update - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sport type.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->sportTypeService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Sport type deleted successfully.',
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
                    'message' => 'Sport type not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sport type.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('SportTypeApiController::destroy - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sport type.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getActive(): JsonResponse
    {
        try {
            $sportTypes = $this->sportTypeService->getActive();

            return response()->json([
                'success' => true,
                'data' => $sportTypes,
            ]);
        } catch (\Exception $e) {
            Log::error('SportTypeApiController::getActive - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve active sport types.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

