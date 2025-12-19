<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventApplication; // Import the model

class EventApplicationController extends Controller
{
    /**
     * Show the form for creating a new event application.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('committee.create-event-application');
    }

    /**
     * Store a newly created event application in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_name' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'proposed_budget' => 'required|numeric|min:0',
        ]);

        EventApplication::create([
            'committee_member_id' => auth()->user()->id,
            'event_name' => $request->event_name,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'proposed_budget' => $request->proposed_budget,
            'status' => 'pending', // Default status
        ]);

        return redirect()->route('homepage')->with('success', 'Event application submitted successfully for review!');
    }
}
