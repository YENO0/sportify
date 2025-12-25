<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import the User model

class HomeController extends Controller
{
    /**
     * Display the homepage based on user role.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $totalUsers = User::count();
            $adminCount = User::where('role', 'admin')->count();
            return view('homepage.admin', compact('totalUsers', 'adminCount'));
        } elseif ($user->isCommittee()) {
            return redirect()->route('committee.dashboard');
        } else {
            return view('homepage.student');
        }
    }
}
