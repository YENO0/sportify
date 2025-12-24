<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use App\Models\Equipment;
use App\Patterns\Factory\MaintenanceFactoryManager;
use App\Patterns\Decorator\MaintenanceQuantityDecorator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
    /**
     * Display the maintenance dashboard
     */
    public function index()
    {
        try {
            // Process completed maintenances and return quantities (Decorator Pattern)
            $this->processCompletedMaintenances();

            // Get upcoming maintenances (next 30 days)
            $upcomingMaintenances = Maintenance::with(['equipment.brand', 'assignedUser'])
                ->where('status', 'pending')
                ->where(function($query) {
                    $query->where('start_date', '>=', now())
                          ->orWhere('scheduled_date', '>=', now());
                })
                ->where(function($query) {
                    $query->where('start_date', '<=', now()->addDays(30))
                          ->orWhere('scheduled_date', '<=', now()->addDays(30));
                })
                ->orderByRaw('COALESCE(start_date, scheduled_date) ASC')
                ->paginate(10);

            // Get overdue maintenances
            $overdueMaintenances = Maintenance::with(['equipment.brand', 'assignedUser'])
                ->where('status', 'pending')
                ->where(function($query) {
                    $query->where('start_date', '<', now())
                          ->orWhere('scheduled_date', '<', now());
                })
                ->orderByRaw('COALESCE(start_date, scheduled_date) ASC')
                ->get();

            // Get in-progress maintenances
            $inProgressMaintenances = Maintenance::with(['equipment.brand', 'assignedUser'])
                ->where('status', 'in_progress')
                ->orderBy('scheduled_date', 'asc')
                ->get();

            // Get statistics
            $stats = $this->getMaintenanceStats();

            return view('maintenance.dashboard', compact(
                'upcomingMaintenances',
                'overdueMaintenances',
                'inProgressMaintenances',
                'stats'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading maintenance dashboard: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 
                'Failed to load maintenance dashboard. Error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Show the form for creating a new maintenance
     */
    public function create()
    {
        try {
            $equipment = Equipment::where('status', '!=', 'retired')
                ->orderBy('name', 'asc')
                ->get();
            $users = \App\Models\User::orderBy('name', 'asc')->get();
            return view('maintenance.create', compact('equipment', 'users'));
        } catch (\Exception $e) {
            Log::error('Error loading maintenance create form: ' . $e->getMessage());
            return redirect()->route('maintenance.index')->with('error', 
                'Failed to load create form. Please try again.'
            );
        }
    }

    /**
     * Store a newly created maintenance using Factory Method pattern
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'equipment_id' => 'required|exists:equipment,id',
                'maintenance_type' => 'required|in:scheduled,emergency,preventive,repair',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'scheduled_date' => 'required|date',
                'start_date' => 'required|date|after_or_equal:scheduled_date',
                'end_date' => 'required|date|after:start_date',
                'quantity' => 'required|integer|min:1',
                'cost' => 'nullable|numeric|min:0',
                'assigned_to' => 'nullable|exists:users,id',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            // Additional validation: Check if equipment has enough quantity
            $equipment = Equipment::findOrFail($request->equipment_id);
            if ($equipment->available_quantity < $request->quantity) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Insufficient quantity. Available: {$equipment->available_quantity}, Requested: {$request->quantity}");
            }

            DB::beginTransaction();

            // Use Factory Method pattern to create maintenance
            // Factory Method will handle quantity deduction based on maintenance type
            $maintenance = MaintenanceFactoryManager::create($request->maintenance_type, $request->all());

            // Decorator Pattern: Apply quantity management decorator
            $quantityDecorator = new MaintenanceQuantityDecorator($maintenance);
            
            // Deduct quantity if maintenance starts immediately (start_date is today or past)
            if ($quantityDecorator->shouldDeductQuantity()) {
                $quantityDecorator->deductQuantityOnStart();
            }

            DB::commit();

            return redirect()->route('maintenance.index')
                ->with('success', 'Maintenance record created successfully using Factory Method pattern!');

        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            Log::warning('Invalid argument in maintenance creation: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid input: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating maintenance: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create maintenance. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update maintenance status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $maintenance = Maintenance::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,in_progress,completed,cancelled',
                'technician_notes' => 'nullable|string',
                'completed_date' => 'nullable|date',
                'cost' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $oldStatus = $maintenance->status;
            
            $maintenance->update([
                'status' => $request->status,
                'technician_notes' => $request->technician_notes,
                'completed_date' => $request->completed_date ?? ($request->status === 'completed' ? now() : null),
                'cost' => $request->cost ?? $maintenance->cost,
            ]);

            // Decorator Pattern: Handle quantity management
            $quantityDecorator = new MaintenanceQuantityDecorator($maintenance);
            
            // If status changed to in_progress and quantity hasn't been deducted, deduct it
            if ($request->status === 'in_progress' && $oldStatus === 'pending') {
                if ($quantityDecorator->shouldDeductQuantity() || 
                    ($maintenance->start_date && $maintenance->start_date->lte(now()))) {
                    try {
                        $quantityDecorator->deductQuantityOnStart();
                    } catch (\Exception $e) {
                        Log::warning('Could not deduct quantity: ' . $e->getMessage());
                    }
                }
            }
            
            // If status changed to completed or end date passed, return quantity
            if (($request->status === 'completed' || $maintenance->shouldReturnQuantity()) && $oldStatus !== 'completed') {
                $quantityDecorator->returnQuantityOnComplete();
            }

            // If completed, update equipment status back to available
            if ($request->status === 'completed' && $maintenance->equipment) {
                $maintenance->equipment->update([
                    'status' => 'available',
                    'last_maintenance_date' => now()
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Maintenance status updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating maintenance status: ' . $e->getMessage(), [
                'maintenance_id' => $id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update maintenance. Error: ' . $e->getMessage());
        }
    }

    /**
     * Process completed maintenances and return quantities (Decorator Pattern)
     */
    private function processCompletedMaintenances(): void
    {
        try {
            // Find maintenances that should return quantity (end date passed but not yet returned)
            $maintenancesToComplete = Maintenance::with('equipment')
                ->whereIn('status', ['pending', 'in_progress'])
                ->whereNotNull('end_date')
                ->where('end_date', '<', now())
                ->get();

            foreach ($maintenancesToComplete as $maintenance) {
                $quantityDecorator = new MaintenanceQuantityDecorator($maintenance);
                if ($quantityDecorator->shouldReturnQuantity()) {
                    DB::beginTransaction();
                    try {
                        $quantityDecorator->returnQuantityOnComplete();
                        $maintenance->update([
                            'status' => 'completed',
                            'completed_date' => now()
                        ]);
                        if ($maintenance->equipment) {
                            $maintenance->equipment->update([
                                'status' => 'available',
                                'last_maintenance_date' => now()
                            ]);
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        Log::error('Error processing completed maintenance: ' . $e->getMessage(), [
                            'maintenance_id' => $maintenance->id
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error processing completed maintenances: ' . $e->getMessage());
        }
    }

    /**
     * Get maintenance statistics
     */
    private function getMaintenanceStats(): array
    {
        try {
            $totalPending = Maintenance::where('status', 'pending')->count();
            $overdue = Maintenance::where('status', 'pending')
                ->where('scheduled_date', '<', now())
                ->count();
            $inProgress = Maintenance::where('status', 'in_progress')->count();
            $completedThisMonth = Maintenance::where('status', 'completed')
                ->whereMonth('completed_date', now()->month)
                ->whereYear('completed_date', now()->year)
                ->count();
            $totalCost = Maintenance::where('status', 'completed')
                ->whereMonth('completed_date', now()->month)
                ->whereYear('completed_date', now()->year)
                ->sum('cost') ?? 0;

            return [
                'total_pending' => $totalPending,
                'overdue' => $overdue,
                'in_progress' => $inProgress,
                'completed_this_month' => $completedThisMonth,
                'total_cost' => $totalCost,
            ];
        } catch (\Exception $e) {
            Log::error('Error calculating maintenance stats: ' . $e->getMessage());
            return [
                'total_pending' => 0,
                'overdue' => 0,
                'in_progress' => 0,
                'completed_this_month' => 0,
                'total_cost' => 0,
            ];
        }
    }
}

