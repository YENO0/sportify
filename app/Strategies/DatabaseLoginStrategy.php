<?php

namespace App\Strategies;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Requests\LoginRequest;

class DatabaseLoginStrategy implements LoginStrategy
{
    public function login(Request $request)
    {
        $request = LoginRequest::createFrom($request);
        
        $request->checkRateLimiting();

        if (Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        )) {
            RateLimiter::clear($request->throttleKey());
            $request->session()->regenerate();

            return redirect()->intended(route('homepage'));
        }

        RateLimiter::hit($request->throttleKey(), 60);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
