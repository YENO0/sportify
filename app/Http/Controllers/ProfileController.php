<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Check if email is being changed
        $emailChanged = $request->input('email') !== $user->email;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
            'birthday' => ['required', 'date', 'before_or_equal:today', 'before_or_equal:' . now()->subYears(16)->format('Y-m-d')],
            'contact' => ['required', 'string', 'max:255'],
        ];

        // Require current password only if email is being changed
        if ($emailChanged) {
            $rules['current_password'] = [
                'required',
                function ($attribute, $value, $fail) use ($user) {
                    if (!Hash::check($value, $user->password)) {
                        $fail('The current password is incorrect.');
                    }
                },
            ];
        }

        $validated = $request->validate($rules, [
            'current_password.required' => 'Current password is required when changing your email address.',
        ]);

        if ($request->hasFile('profile_picture')) {
            $image = $request->file('profile_picture');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('profile_pictures'), $imageName);
            $user->profile_picture = $imageName;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->gender = $validated['gender'] ?? $user->gender;
        $user->birthday = $validated['birthday'] ?? $user->birthday;
        $user->contact = $validated['contact'] ?? $user->contact;

        if (!empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        $message = 'Your profile has been updated successfully!';
        if ($emailChanged) {
            $message .= ' Please note: Your email address has been changed.';
        }

        return redirect()->route('profile.show')
            ->with('success', $message);
    }
}
