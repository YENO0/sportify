<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\EquipmentTransaction;
use App\Services\EquipmentService;
use App\Services\SportTypeService;
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
    protected $equipmentService;
    protected $sportTypeService;

    public function __construct(EquipmentService $equipmentService, SportTypeService $sportTypeService)
    {
        $this->equipmentService = $equipmentService;
        $this->sportTypeService = $sportTypeService;
    }

    /**
     * Display the inventory dashboard
     */
    public function index(Request $request)
    {
        try {
            $filters = $request->only(['search', 'status', 'sport_type_id', 'low_stock', 'sort', 'direction']);
            $equipment = $this->equipmentService->getAll($filters, 10);
            $equipment->appends($request->query());

            $stats = $this->equipmentService->getDashboardStats();
            $lowStockEquipment = $this->equipmentService->getLowStockEquipment();
            $sportTypes = $this->sportTypeService->getActive();

            return view('inventory.dashboard', compact('equipment', 'stats', 'lowStockEquipment', 'sportTypes'));
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
            $sportTypes = $this->sportTypeService->getActive();
            $selectedBrandId = $request->get('brand_id');
            $selectedSportTypeId = $request->get('sport_type_id');
            return view('inventory.create', compact('brands', 'sportTypes', 'selectedBrandId', 'selectedSportTypeId'));
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
                'sport_type_id' => 'required|exists:sport_types,id',
                'brand_id' => 'nullable|exists:brands,id',
                'model' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'quantity' => 'required|integer|min:1',
                'price' => 'nullable|numeric|min:0',
                'location' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'images' => 'required|array|min:1',
                'images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max per image
            ]);

            // Custom validation: At least one image is required
            if (!$request->hasFile('images') || count($request->file('images')) === 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'At least one image is required.');
            }
            
            // Additional validation: Check each image
            foreach ($request->file('images') as $index => $image) {
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', "Image " . ($index + 1) . " is invalid or corrupted.");
                }
            }

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            DB::beginTransaction();

            // Prepare data for factory
            $equipmentData = $request->all();
            
            // Handle image uploads - will be processed by Factory Method using Decorator Pattern
            $images = $request->file('images', []);
            $imageAltTexts = $request->input('image_alt_texts', []);
            
            // Store images in data array for factory to process
            $equipmentData['images'] = $images;
            $equipmentData['image_alt_texts'] = $imageAltTexts;

            // Use Factory Method pattern to create equipment based on sport type
            // Factory Method will handle image processing in postCreation using Decorator Pattern
            $equipment = EquipmentFactoryManager::create($request->sport_type_id, $equipmentData);

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
            $equipment = $this->equipmentService->getById($id);
            $equipment->load(['transactions.user', 'images']);

            return view('inventory.show', compact('equipment'));
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404 || $e->getPrevious() instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                throw new EquipmentNotFoundException($id, $e->getPrevious() ?? $e);
            }
            Log::error('Error showing equipment: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load equipment details. Error: ' . $e->getMessage());
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
            $equipment = $this->equipmentService->getById($id);
            $brands = \App\Models\Brand::orderBy('name', 'asc')->get();
            $sportTypes = $this->sportTypeService->getActive();
            return view('inventory.edit', compact('equipment', 'brands', 'sportTypes'));
        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404 || $e->getPrevious() instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                throw new EquipmentNotFoundException($id, $e->getPrevious() ?? $e);
            }
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->route('inventory.index')
                ->with('error', 'Failed to load edit form. Error: ' . $e->getMessage());
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
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'sport_type_id' => 'required|exists:sport_types,id',
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

            $this->equipmentService->update($id, $validator->validated());

            return redirect()->route('inventory.show', $id)
                ->with('success', 'Equipment updated successfully!');

        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404 || $e->getPrevious() instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                throw new EquipmentNotFoundException($id, $e->getPrevious() ?? $e);
            }
            Log::error('Error updating equipment: ' . $e->getMessage(), [
                'equipment_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update equipment. Error: ' . $e->getMessage());
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
            $this->equipmentService->delete($id);

            return redirect()->route('inventory.index')
                ->with('success', 'Equipment deleted successfully!');

        } catch (\RuntimeException $e) {
            if ($e->getCode() === 404 || $e->getPrevious() instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                throw new EquipmentNotFoundException($id, $e->getPrevious() ?? $e);
            }
            Log::error('Error deleting equipment: ' . $e->getMessage(), [
                'equipment_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete equipment. Error: ' . $e->getMessage());
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

            $this->equipmentService->checkout($id, $validator->validated()['quantity'], $validator->validated()['notes'] ?? null);

            return redirect()->route('inventory.show', $id)
                ->with('success', 'Equipment checked out successfully!');

        } catch (EquipmentNotFoundException | EquipmentStatusException | InsufficientQuantityException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
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
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $this->equipmentService->return($id, $validator->validated()['quantity'], $validator->validated()['notes'] ?? null);

            return redirect()->route('inventory.show', $id)
                ->with('success', 'Equipment returned successfully!');

        } catch (EquipmentNotFoundException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('Error returning equipment: ' . $e->getMessage(), [
                'equipment_id' => $id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to return equipment. Error: ' . $e->getMessage());
        }
    }
}
