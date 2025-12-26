<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Import the User model
use App\Models\Event;
use App\Services\EventStatusService;

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
            // Keep lifecycle/registration statuses current before listing
            EventStatusService::syncAll();

            $now = now();

            // Upcoming = approved + upcoming/ongoing + start date is today or later
            $upcomingEvents = Event::query()
                ->where('status', 'approved')
                ->whereIn('event_status', ['Upcoming', 'Ongoing'])
                ->whereDate('event_start_date', '>=', $now->toDateString())
                ->orderBy('event_start_date', 'asc')
                ->take(6)
                ->get();

            // Recent = approved + ended within the last month (based on start date)
            $recentEvents = Event::query()
                ->where('status', 'approved')
                ->whereDate('event_start_date', '<', $now->toDateString())
                ->whereDate('event_start_date', '>=', $now->copy()->subMonth()->toDateString())
                ->orderBy('event_start_date', 'desc')
                ->take(6)
                ->get();

            return view('homepage.student', compact('upcomingEvents', 'recentEvents'));
        }
    }
}
