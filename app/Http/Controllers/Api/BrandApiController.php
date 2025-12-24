<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BrandApiController extends Controller
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'sort', 'direction']);
            $perPage = $request->get('per_page', 15);
            
            $brands = $this->brandService->getAll($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $brands->items(),
                'meta' => [
                    'current_page' => $brands->currentPage(),
                    'last_page' => $brands->lastPage(),
                    'per_page' => $brands->perPage(),
                    'total' => $brands->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('BrandApiController::index - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brands.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:brands,name',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $brand = $this->brandService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully.',
                'data' => $brand,
            ], 201);
        } catch (\Exception $e) {
            Log::error('BrandApiController::store - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create brand.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $brand = $this->brandService->getById($id);

            return response()->json([
                'success' => true,
                'data' => $brand,
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brand.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('BrandApiController::show - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve brand.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255|unique:brands,name,' . $id,
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $brand = $this->brandService->update($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully.',
                'data' => $brand,
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Brand not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('BrandApiController::update - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->brandService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Brand deleted successfully.',
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
                    'message' => 'Brand not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('BrandApiController::destroy - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

