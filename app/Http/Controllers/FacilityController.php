<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use App\Exceptions\FacilityNotFoundException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = Facility::all();
        return view('facilities.index', compact('facilities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('facilities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Indoor,Outdoor',
            'status' => 'required|string|in:Active,Maintenance,Emergency Closure',
            'description' => 'nullable|string', // Added validation for description
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('facilities', 'public');
            $data['image'] = $path;
        }

        Facility::create($data);

        return redirect()->route('facilities.index')
                         ->with('success', 'Facility created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Facility $facility)
    {
        return view('facilities.show', compact('facility'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Facility $facility)
    {
        return view('facilities.edit', compact('facility'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Facility $facility)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:Indoor,Outdoor',
            'status' => 'required|string|in:Active,Booked,Maintenance,Emergency Closure',
            'description' => 'nullable|string', // Added validation for description
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($facility->image) {
                Storage::disk('public')->delete($facility->image);
            }
            $path = $request->file('image')->store('facilities', 'public');
            $data['image'] = $path;
        }

        try {
            // The Facility model is already resolved by Route Model Binding.
            // If it wasn't found, a 404 would have been thrown before this point.
            $facility->update($data);

            return redirect()->route('facilities.index')
                             ->with('success', 'Facility updated successfully');
        } catch (\Exception $e) { // Catching a generic exception for unexpected errors
            // Log the exception for debugging purposes
            \Log::error("Error updating facility {$facility->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update facility. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Facility $facility)
    {
        try {
            // The Facility model is already resolved by Route Model Binding.
            // If it wasn't found, a 404 would have been thrown before this point.
            if ($facility->image) {
                Storage::disk('public')->delete($facility->image);
            }
            $facility->delete();
            return redirect()->route('facilities.index')
                             ->with('success', 'Facility deleted successfully');
        } catch (\Exception $e) { // Catching a generic exception for unexpected errors
            // Log the exception for debugging purposes
            \Log::error("Error deleting facility {$facility->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete facility. Please try again.');
        }
    }

    public function getFacilityPhoto($filename)
    {
        // Corrected path: images are stored in storage/app/public/facilities
        $path = storage_path('app/public/facilities/' . $filename);

        // Laravel's response()->file() handles file existence and content type automatically
        return response()->file($path);
    }
}
