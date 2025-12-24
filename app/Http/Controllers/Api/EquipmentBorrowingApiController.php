<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EquipmentBorrowingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EquipmentBorrowingApiController extends Controller
{
    protected $borrowingService;

    public function __construct(EquipmentBorrowingService $borrowingService)
    {
        $this->borrowingService = $borrowingService;
    }

    /**
     * Get available equipment for borrowing
     */
    public function getAvailableEquipment(int $event): JsonResponse
    {
        try {
            $equipment = $this->borrowingService->getAvailableEquipment();
            $borrowedIds = $this->borrowingService->getBorrowedEquipmentIds($event);

            return response()->json([
                'success' => true,
                'data' => [
                    'equipment' => $equipment,
                    'borrowed_equipment_ids' => $borrowedIds,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('EquipmentBorrowingApiController::getAvailableEquipment - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve available equipment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store newly created borrowings
     */
    public function store(Request $request, int $event): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'equipment' => 'required|array|min:1',
                'equipment.*' => 'required|exists:equipment,id|distinct',
                'quantity' => 'required|array|min:1',
                'quantity.*' => 'required|integer|min:1',
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
            $result = $this->borrowingService->createBorrowings($event, $data);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'borrowings' => $result['borrowings'],
                ],
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('EquipmentBorrowingApiController::store - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create equipment borrowings.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Return/Delete a borrowing
     */
    public function destroy(int $event, int $borrowing): JsonResponse
    {
        try {
            $this->borrowingService->returnBorrowing($event, $borrowing);

            return response()->json([
                'success' => true,
                'message' => 'Equipment returned successfully.',
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('EquipmentBorrowingApiController::destroy - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to return equipment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

