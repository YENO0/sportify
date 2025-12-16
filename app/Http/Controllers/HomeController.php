<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Display the homepage based on user role.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return view('homepage.admin');
        } elseif ($user->isCommittee()) {
            return view('homepage.committee');
        } else {
            return view('homepage.student');
        }
    }
}
