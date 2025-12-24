<?php

namespace App\Http\Controllers;

use App\Models\FacilityMaintenance;
use App\Models\Facility; // Import Facility model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import Rule for validation

class FacilityMaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $maintenances = FacilityMaintenance::with('facility')->orderBy('start_date', 'asc')->get();
        return view('facilities.maintenance.index', compact('maintenances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $facilities = Facility::all();
        return view('facilities.maintenance.create', compact('facilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Overlap Validation
        $conflictingMaintenance = FacilityMaintenance::where('facility_id', $request->facility_id)
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_date', '<=', $request->start_date)
                      ->where('end_date', '>', $request->start_date);
                })->orWhere(function ($q) use ($request) {
                    $q->where('start_date', '<', $request->end_date)
                      ->where('end_date', '>=', $request->end_date);
                });
            })->exists();

        if ($conflictingMaintenance) {
            return back()->withErrors(['date_overlap' => 'There is an overlapping maintenance schedule for this facility.'])->withInput();
        }

        FacilityMaintenance::create($request->all());

        return redirect()->route('facilities.maintenance.index')
                         ->with('success', 'Facility maintenance scheduled successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FacilityMaintenance $facilityMaintenance)
    {
        $facilities = Facility::all();
        return view('facilities.maintenance.edit', compact('facilityMaintenance', 'facilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FacilityMaintenance $facilityMaintenance)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Overlap Validation, excluding the current maintenance record
        $conflictingMaintenance = FacilityMaintenance::where('facility_id', $request->facility_id)
            ->where('id', '!=', $facilityMaintenance->id) // Exclude current record
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_date', '<=', $request->start_date)
                      ->where('end_date', '>', $request->start_date);
                })->orWhere(function ($q) use ($request) {
                    $q->where('start_date', '<', $request->end_date)
                      ->where('end_date', '>=', $request->end_date);
                });
            })->exists();

        if ($conflictingMaintenance) {
            return back()->withErrors(['date_overlap' => 'There is an overlapping maintenance schedule for this facility.'])->withInput();
        }

        $facilityMaintenance->update($request->all());

        return redirect()->route('facilities.maintenance.index')
                         ->with('success', 'Facility maintenance updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FacilityMaintenance $facilityMaintenance)
    {
        $facilityMaintenance->delete();

        return redirect()->route('facilities.maintenance.index')
                         ->with('success', 'Facility maintenance deleted successfully.');
    }
}