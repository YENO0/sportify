<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of brands
     */
    public function index(Request $request)
    {
        try {
            $query = Brand::withCount('equipment');

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
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

            $brands = $query->paginate(15)->appends($request->query());

            return view('brands.index', compact('brands'));
        } catch (\Exception $e) {
            Log::error('Error loading brands: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 
                'Failed to load brands. Error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Show the form for creating a new brand
     */
    public function create()
    {
        try {
            return view('brands.create');
        } catch (\Exception $e) {
            Log::error('Error loading brand create form: ' . $e->getMessage());
            return redirect()->route('brands.index')->with('error', 
                'Failed to load create form. Please try again.'
            );
        }
    }

    /**
     * Store a newly created brand
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:brands,name',
                'description' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'contact_email' => 'nullable|email|max:255',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            DB::beginTransaction();

            $brand = Brand::create($request->all());

            DB::commit();

            return redirect()->route('brands.index')
                ->with('success', 'Brand "' . $brand->name . '" registered successfully!');

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            if ($e->getCode() == 23000) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'A brand with this name already exists.');
            }
            Log::error('Database error creating brand: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create brand. Please try again.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating brand: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create brand. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified brand
     */
    public function show($id)
    {
        try {
            $brand = Brand::with('equipment')->findOrFail($id);
            return view('brands.show', compact('brand'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Brand not found: {$id}");
            return redirect()->route('brands.index')
                ->with('error', 'Brand not found.');
        } catch (\Exception $e) {
            Log::error('Error showing brand: ' . $e->getMessage());
            return redirect()->route('brands.index')
                ->with('error', 'Failed to load brand details. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified brand
     */
    public function edit($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            return view('brands.edit', compact('brand'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('brands.index')
                ->with('error', 'Brand not found.');
        } catch (\Exception $e) {
            Log::error('Error loading edit form: ' . $e->getMessage());
            return redirect()->route('brands.index')
                ->with('error', 'Failed to load edit form. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified brand
     */
    public function update(Request $request, $id)
    {
        try {
            $brand = Brand::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:brands,name,' . $id,
                'description' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'contact_email' => 'nullable|email|max:255',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validation failed. Please check your input.');
            }

            $brand->update($request->all());

            return redirect()->route('brands.show', $id)
                ->with('success', 'Brand updated successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('brands.index')
                ->with('error', 'Brand not found.');
        } catch (\Exception $e) {
            Log::error('Error updating brand: ' . $e->getMessage(), [
                'brand_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update brand. Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified brand (soft delete)
     */
    public function destroy($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brandName = $brand->name;
            $brand->delete();

            return redirect()->route('brands.index')
                ->with('success', 'Brand "' . $brandName . '" deleted successfully!');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('brands.index')
                ->with('error', 'Brand not found.');
        } catch (\Exception $e) {
            Log::error('Error deleting brand: ' . $e->getMessage(), [
                'brand_id' => $id
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete brand. Error: ' . $e->getMessage());
        }
    }
}

