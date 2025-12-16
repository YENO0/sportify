<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CommitteeController extends Controller
{
    /**
     * Show the form for creating a new committee member.
     */
    public function create()
    {
        return view('admin.create-committee');
    }

    /**
     * Store a newly created committee member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Create committee member with committee role
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'], // Will be hashed by the model's casts
            'role' => User::ROLE_COMMITTEE,
        ]);

        return redirect()->route('admin.committee.create')
            ->with('success', "Committee member '{$user->name}' has been created successfully!");
    }
}
