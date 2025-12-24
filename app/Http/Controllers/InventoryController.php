<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentTransaction;
use App\Patterns\Factory\EquipmentFactoryManager;
use App\Patterns\Decorator\EquipmentDecoratorManager;
use App\Exceptions\EquipmentNotFoundException;
use App\Exceptions\InsufficientQuantityException;
use App\Exceptions\EquipmentStatusException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display the inventory dashboard
     */
    public function index()
    {
        try {
            $equipment = Equipment::with(['features', 'brand'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $stats = $this->getDashboardStats();
            
            // Get low stock equipment for alerts
            $lowStockEquipment = Equipment::whereColumn('available_quantity', '<', 'minimum_stock_amount')
                ->where('status', '!=', 'retired')
                ->with('brand')
                ->orderByRaw('(available_quantity - minimum_stock_amount) ASC')
                ->get();

            return view('inventory.dashboard', compact('equipment', 'stats', 'lowStockEquipment'));
        } catch (\Exception $e) {
            Log::error('Error loading inventory dashboard: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 
                'Failed to load inventory dashboard. Error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Show the form for creating a new equipment
     */
    public function create(Request $request)
    {
        try {
            $brands = \App\Models\Brand::orderBy('name', 'asc')->get();
            $selectedBrandId = $request->get('brand_id');
            return view('inventory.create', compact('brands', 'selectedBrandId'));
        } catch (\Exception $e) {
            Log::error('Error loading create form: ' . $e->getMessage());
            return redirect()->route('inventory.index')->with('error', 
                'Failed to load create form. Please try again.'
            );
        }
    }

    /**
     * Store a newly created equipment using Factory Method pattern
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'type' => 'required|in:sports,gym,outdoor',
                'brand_id' => 'nullable|exists:brands,id',
                'model' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:1',
                'price' => 'nullable|numeric|min:0',
                'location' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            DB::beginTransaction();

            // Use Factory Method pattern to create equipment
            // Factory Method will set default minimum_stock_amount based on equipment type
            $equipment = EquipmentFactoryManager::create($request->type, $request->all());

            // Apply decorators if specified
            $decoratorManager = new EquipmentDecoratorManager($equipment);
            
            if ($request->has('add_insurance') && $request->add_insurance) {
                $decoratorManager->withInsurance(
                    $request->insurance_cost ?? 0,
                    $request->insurance_expiry ? \Carbon\Carbon::parse($request->insurance_expiry) : null
                );
            }

            if ($request->has('add_warranty') && $request->add_warranty) {
                $decoratorManager->withWarranty(
                    $request->warranty_type ?? 'Standard',
                    $request->warranty_expiry ? \Carbon\Carbon::parse($request->warranty_expiry) : null
                );
            }

            if ($request->has('add_maintenance_tracking') && $request->add_maintenance_tracking) {
                $decoratorManager->withMaintenanceTracking(
                    $request->maintenance_interval ?? 3
                );
            }

            // Decorator Pattern: Add low stock alert feature
            if ($request->has('add_low_stock_alert') && $request->add_low_stock_alert) {
                $decoratorManager->withLowStockAlert(
                    true,
                    $request->alert_email ?? null
                );
            }

            $decoratorManager->apply();

            DB::commit();

            return redirect()->route('inventory.index')
                ->with('success', 'Equipment created successfully using Factory Method pattern!');

        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            Log::warning('Invalid argument in equipment creation: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid input: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating equipment: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create equipment. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified equipment
     */
    public function show($id)
    {
        try {
            $equipment = Equipment::with(['features', 'transactions.user', 'brand'])
                ->findOrFail($id);

            return view('inventory.show', compact('equipment'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Equipment not found: {$id}");
            throw new EquipmentNotFoundException($id, $e);
        } catch (\Exception $e) {
            Log::error('Error showing equipment: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load equipment details. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified equipment
     */
    public function edit($id)
    {
        try {
            $equipment = Equipment::with('features')->findOrFail($id);
            $brands = \App\Models\Brand::orderBy('name', 'asc')->get();
            return view('inventory.edit', compact('equipment', 'brands'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EquipmentNotFoundException($id, $e);
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load edit form. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified equipment
     */
    public function update(Request $request, $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'brand_id' => 'nullable|exists:brands,id',
                'model' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:0',
                'available_quantity' => 'required|integer|min:0|lte:quantity',
                'minimum_stock_amount' => 'nullable|integer|min:0',
                'price' => 'nullable|numeric|min:0',
                'status' => 'required|in:available,maintenance,damaged,retired',
                'location' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'last_maintenance_date' => 'nullable|date',
                'next_maintenance_date' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            $equipment->update($request->all());

            return redirect()->route('inventory.show', $id)
                ->with('success', 'Equipment updated successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EquipmentNotFoundException($id, $e);
        } catch (\Exception $e) {
            Log::error('Error updating equipment: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update equipment. Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified equipment (soft delete)
     */
    public function destroy($id)
    {
        try {
            $equipment = Equipment::findOrFail($id);
            $equipment->delete();

            return redirect()->route('inventory.index')
                ->with('success', 'Equipment deleted successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EquipmentNotFoundException($id, $e);
        } catch (\Exception $e) {
            Log::error('Error deleting equipment: ' . $e->getMessage(), [
                'equipment_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete equipment. Error: ' . $e->getMessage());
        }
    }

    /**
     * Checkout equipment
     */
    public function checkout(Request $request, $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
                'user_id' => 'nullable|exists:users,id',
                'expected_return_date' => 'nullable|date|after:today',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            if ($equipment->status !== 'available') {
                throw new EquipmentStatusException($id, $equipment->status, 'available');
            }

            if ($equipment->available_quantity < $request->quantity) {
                throw new InsufficientQuantityException(
                    $id,
                    $request->quantity,
                    $equipment->available_quantity
                );
            }

            DB::beginTransaction();

            $equipment->available_quantity -= $request->quantity;
            $equipment->save();

            EquipmentTransaction::create([
                'equipment_id' => $equipment->id,
                'transaction_type' => 'checkout',
                'user_id' => $request->user_id,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'transaction_date' => now(),
                'expected_return_date' => $request->expected_return_date,
            ]);

            DB::commit();

            return redirect()->route('inventory.show', $id)
                ->with('success', 'Equipment checked out successfully!');

        } catch (EquipmentStatusException | InsufficientQuantityException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EquipmentNotFoundException($id, $e);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error checking out equipment: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to checkout equipment. Error: ' . $e->getMessage());
        }
    }

    /**
     * Return equipment
     */
    public function return(Request $request, $id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();

            $equipment->available_quantity += $request->quantity;
            
            if ($equipment->available_quantity > $equipment->quantity) {
                throw new \InvalidArgumentException('Returned quantity exceeds total quantity.');
            }
            
            $equipment->save();

            EquipmentTransaction::create([
                'equipment_id' => $equipment->id,
                'transaction_type' => 'return',
                'user_id' => $request->user_id ?? null,
                'quantity' => $request->quantity,
                'notes' => $request->notes,
                'transaction_date' => now(),
            ]);

            DB::commit();

            return redirect()->route('inventory.show', $id)
                ->with('success', 'Equipment returned successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new EquipmentNotFoundException($id, $e);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error returning equipment: ' . $e->getMessage(), [
                'equipment_id' => $id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to return equipment. Error: ' . $e->getMessage());
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats(): array
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
            Log::error('Error calculating dashboard stats: ' . $e->getMessage());
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
}

