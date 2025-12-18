<?php

namespace App\Strategies;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class StubLoginStrategy implements LoginStrategy
{
    public function login(Request $request)
    {
        $user = User::first();
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->intended(route('homepage'));
    }
}
