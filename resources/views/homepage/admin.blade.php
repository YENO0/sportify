@extends('layouts.app')

@section('content')
<style>
    /* Full-width layout overrides and re-usable styles */
    body { display: block; background: #f8fafc; }
    .card {
        max-width: none; width: 100%; min-height: 100vh;
        border-radius: 0; padding: 0; background: #f8fafc; /* Light background */
        border: none; box-shadow: none;
    }
    .navbar {
        display: flex; justify-content: space-between; align-items: center;
        padding: 1rem 2rem; background: rgba(255, 255, 255, 0.8); /* Lighter navbar background */
        border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        position: sticky; top: 0; z-index: 10; backdrop-filter: blur(8px);
    }
    .navbar .brand { font-size: 1.5rem; font-weight: 700; color: #1f2937; /* Darker text */ }
    .navbar .user-info { display: flex; align-items: center; gap: 1rem; }
    .navbar .user-info span { font-size: 0.9rem; color: #4b5563; /* Darker text */ }
    .navbar .logout-btn {
        background: #e5e7eb; /* Light background */
        border: none; color: #374151; /* Darker text */
        padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer;
        font-size: 0.8rem; font-weight: 600; transition: background 0.2s ease;
    }
    .navbar .logout-btn:hover { background: #d1d5db; /* Darker hover background */ }
    .main-footer {
        text-align: center; padding: 2rem; margin-top: 2rem;
        background: #f0f4f8; /* Light grey background */
        border-top: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        font-size: 0.9rem; color: #4b5563; /* Darker text */
    }
    .footer-links { display: flex; justify-content: center; gap: 1.5rem; margin-bottom: 1rem; }
    .footer-links a { color: #4b5563; text-decoration: none; font-size: 0.9rem; }
    .footer-links a:hover { color: #1f2937; /* Even darker on hover */ text-decoration: underline; }

    /* Page-specific styles */
    .page-header {
        background: #ffffff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        padding: 2rem 2.5rem;
    }
    .page-header h1 { font-size: 1.8rem; font-weight: 700; color: #1f2937; margin: 0;}
    .page-header p { font-size: 1rem; color: #4b5563; margin-top: 0.25rem; }

    .content-area { padding: 2.5rem; max-width: 1400px; margin: 0 auto; }
    .section-title {
        font-size: 1.5rem; font-weight: 600; color: #1f2937;
        margin-bottom: 1.5rem; padding-bottom: 0.5rem;
    }
    .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
    
    .stat-card {
        background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem; padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .stat-card .label { font-size: 0.9rem; font-weight: 500; color: #4b5563; margin-bottom: 0.5rem; }
    .stat-card .value { font-size: 2.2rem; font-weight: 700; color: #1d4ed8; }
    
    .action-card {
        background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem; padding: 2rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .action-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .action-card h3 { font-size: 1.2rem; font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; }
    .action-card p { font-size: 0.9rem; color: #4b5563; margin-bottom: 1.5rem; }
    .action-card a {
        text-decoration: none; color: #2563eb; font-weight: 600;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .action-card a:hover { color: #1d4ed8; }

    .activity-log {
        background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem;
    }
    .activity-log ul { list-style: none; margin: 0; padding: 0; }
    .activity-log li {
        padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        display: flex; justify-content: space-between; align-items: center;
    }
    .activity-log li:last-child { border-bottom: none; }
    .activity-log .time { font-size: 0.85rem; color: #6b7280; }
</style>

<nav class="navbar">
    <a href="{{ route('homepage') }}" class="brand" style="text-decoration:none;">Sportify</a>
    <div class="user-info">
        <span>{{ auth()->user()->name }} (Admin)</span>
        <a href="{{ route('profile.show') }}" class="logout-btn" style="text-decoration: none; background: #e5e7eb;">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </div>
</nav>

<header class="page-header">
    <h1>Administrator Dashboard</h1>
    <p>Oversee system activity, manage users, and configure settings.</p>
</header>

<main class="content-area">
    <!-- Stat Cards -->
    <div class="grid-container" style="margin-bottom: 2.5rem;">
        <div class="stat-card">
            <div class="label">Total Users</div>
            <div class="value">{{ $totalUsers }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Pending Event Applications</div>
            <div class="value">3</div>
        </div>
        <div class="stat-card">
            <div class="label">Total Events</div>
            <div class="value">12</div>
        </div>
        <div class="stat-card">
            <div class="label">Admins</div>
            <div class="value" style="color: #be123c;">{{ $adminCount }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="margin-bottom: 2.5rem;">
        <h2 class="section-title">Quick Actions</h2>
        <div class="grid-container" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
            <div class="action-card">
                <h3>Manage All Users</h3>
                <p>View, edit, or delete user accounts.</p>
                <a href="{{ route('admin.users.index') }}">Go to User Management &rarr;</a>
            </div>
            <div class="action-card">
                <h3>Create Committee</h3>
                <p>Appoint a new committee member.</p>
                <a href="{{ route('admin.committee.create') }}">Create Member &rarr;</a>
            </div>
            <div class="action-card">
                <h3>Review Applications</h3>
                <p>Approve or reject new event proposals.</p>
                <a href="#">View Applications &rarr;</a>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div>
        <h2 class="section-title">Recent Activity</h2>
        <div class="activity-log">
            <ul>
                <li>
                    <span>New user registered: <strong>john.doe@example.com</strong></span>
                    <span class="time">5 minutes ago</span>
                </li>
                <li>
                    <span>Event Application "Rock Climbing Workshop" was submitted.</span>
                    <span class="time">1 hour ago</span>
                </li>
                <li>
                    <span>User <strong>jane.smith@example.com</strong> updated their profile.</span>
                    <span class="time">3 hours ago</span>
                </li>
                 <li>
                    <span>Admin <strong>{{ auth()->user()->name }}</strong> logged in.</span>
                    <span class="time">6 hours ago</span>
                </li>
            </ul>
        </div>
    </div>
</main>

<footer class="main-footer">
    <div class="footer-links">
        <a href="{{ route('homepage') }}">Home</a>
        <a href="{{ route('about') }}">About Us</a>
    </div>
    &copy; {{ date('Y') }} Sportify. All Rights Reserved.
</footer>
@endsection