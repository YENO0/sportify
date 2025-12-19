<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if the user is authenticated and if their profile is incomplete
        if ($user && (!$user->gender || !$user->birthday || !$user->contact)) {
            // Allow access to the profile editing/update page and logout route to avoid redirect loops
            if (!$request->routeIs('profile.edit') && !$request->routeIs('profile.update') && !$request->routeIs('logout')) {
                return redirect()->route('profile.edit')
                    ->with('warning', 'Please complete your profile before proceeding.');
            }
        }

        return $next($request);
    }
}
