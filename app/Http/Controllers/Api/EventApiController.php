<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class EventApiController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of events
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['search', 'status', 'sort', 'direction']);
            $perPage = $request->get('per_page', 15);
            
            $events = $this->eventService->getAll($filters, $perPage);
            
            return response()->json([
                'success' => true,
                'data' => $events->items(),
                'meta' => [
                    'current_page' => $events->currentPage(),
                    'last_page' => $events->lastPage(),
                    'per_page' => $events->perPage(),
                    'total' => $events->total(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('EventApiController::index - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve events.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created event
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_date' => 'required|date|after_or_equal:start_date',
                'end_time' => 'required|date_format:H:i',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $event = $this->eventService->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully.',
                'data' => $event,
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('EventApiController::store - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create event.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified event
     */
    public function show(int $id): JsonResponse
    {
        try {
            $event = $this->eventService->getById($id);

            return response()->json([
                'success' => true,
                'data' => $event,
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve event.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('EventApiController::show - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve event.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified event
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'start_date' => 'sometimes|required|date',
                'start_time' => 'sometimes|required|date_format:H:i',
                'end_date' => 'sometimes|required|date|after_or_equal:start_date',
                'end_time' => 'sometimes|required|date_format:H:i',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $validator->validated();
            $event = $this->eventService->update($id, $data);

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully.',
                'data' => $event,
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
                    'message' => 'Event not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to update event.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('EventApiController::update - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update event.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified event
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->eventService->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully.',
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete event.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('EventApiController::destroy - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete event.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test return logic
     */
    public function testReturn(Request $request, int $event): JsonResponse
    {
        try {
            $result = $this->eventService->testReturnLogic($event);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'returned_count' => $result['returned_count'],
                ],
            ]);
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => false,
                    'message' => 'Event not found.',
                    'error' => $e->getMessage(),
                ], 404);
            }
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute test return logic.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('EventApiController::testReturn - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute test return logic.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Process automatic returns
     */
    public function processAutomaticReturns(): JsonResponse
    {
        try {
            $result = $this->eventService->processAutomaticReturns();

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => [
                    'total_returned' => $result['total_returned'],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('EventApiController::processAutomaticReturns - Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process automatic returns.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

