<?php

namespace App\Http\Controllers;

use App\Models\SportType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SportTypeController extends Controller
{
    /**
     * Display a listing of sport types.
     */
    public function index(Request $request)
    {
        try {
            $query = SportType::withCount('equipment');

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            // Filter by active status
            if ($request->has('is_active') && $request->get('is_active') !== '') {
                $isActive = $request->get('is_active') == '1' || $request->get('is_active') === 1 || $request->get('is_active') === true;
                $query->where('is_active', $isActive);
            }

            // Sorting
            $sortColumn = $request->get('sort', 'name');
            $sortDirection = $request->get('direction', 'asc');
            
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

            $sportTypes = $query->paginate(15)->appends($request->query());

            return view('sport-types.index', compact('sportTypes'));
        } catch (\Exception $e) {
            Log::error('Error loading sport types: ' . $e->getMessage());
            return redirect()->back()->with('error', 
                'Failed to load sport types. Error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Show the form for creating a new sport type.
     */
    public function create()
    {
        try {
            return view('sport-types.create');
        } catch (\Exception $e) {
            Log::error('Error loading sport type create form: ' . $e->getMessage());
            return redirect()->route('sport-types.index')->with('error', 
                'Failed to load create form. Please try again.'
            );
        }
    }

    /**
     * Store a newly created sport type.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:sport_types,name',
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            DB::beginTransaction();

            $sportType = SportType::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            return redirect()->route('inventory.index', ['tab' => 'sport-types'])
                ->with('success', 'Sport type created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sport type: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create sport type. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified sport type.
     */
    public function show($id)
    {
        try {
            $sportType = SportType::with(['equipment.brand', 'equipment.images'])
                ->withCount('equipment')
                ->findOrFail($id);

            return view('sport-types.show', compact('sportType'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Sport type not found: {$id}");
            return redirect()->route('sport-types.index')
                ->with('error', 'Sport type not found.');
        } catch (\Exception $e) {
            Log::error('Error showing sport type: ' . $e->getMessage());
            return redirect()->route('sport-types.index')
                ->with('error', 'Failed to load sport type details. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified sport type.
     */
    public function edit($id)
    {
        try {
            $sportType = SportType::findOrFail($id);
            return view('sport-types.edit', compact('sportType'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Sport type not found: {$id}");
            return redirect()->route('sport-types.index')
                ->with('error', 'Sport type not found.');
        } catch (\Exception $e) {
            Log::error('Error loading sport type edit form: ' . $e->getMessage());
            return redirect()->route('sport-types.index')->with('error', 
                'Failed to load edit form. Please try again.'
            );
        }
    }

    /**
     * Update the specified sport type.
     */
    public function update(Request $request, $id)
    {
        try {
            $sportType = SportType::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:sport_types,name,' . $id,
                'description' => 'nullable|string',
                'icon' => 'nullable|string|max:255',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            DB::beginTransaction();

            $sportType->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'description' => $request->description,
                'icon' => $request->icon,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            DB::commit();

            return redirect()->route('inventory.index', ['tab' => 'sport-types'])
                ->with('success', 'Sport type updated successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning("Sport type not found: {$id}");
            return redirect()->route('sport-types.index')
                ->with('error', 'Sport type not found.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sport type: ' . $e->getMessage(), [
                'sport_type_id' => $id
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update sport type. Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified sport type.
     */
    public function destroy($id)
    {
        try {
            $sportType = SportType::findOrFail($id);

            // Check if sport type has equipment
            if ($sportType->equipment()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'Cannot delete sport type. It has associated equipment. Please remove or reassign equipment first.');
            }

            DB::beginTransaction();

            $sportType->delete();

            DB::commit();

            return redirect()->route('sport-types.index')
                ->with('success', 'Sport type deleted successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning("Sport type not found: {$id}");
            return redirect()->route('sport-types.index')
                ->with('error', 'Sport type not found.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting sport type: ' . $e->getMessage(), [
                'sport_type_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete sport type. Error: ' . $e->getMessage());
        }
    }
}

