@extends('layouts.app')

@section('content')
<style>
    /* Full-width layout overrides and re-usable styles */
    body { display: block; }
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
    .navbar a.logout-btn {
        text-decoration: none; background: #e5e7eb; /* Light background */
        border: none; color: #374151; /* Darker text */
        padding: 0.5rem 1rem; border-radius: 0.5rem; cursor: pointer;
        font-size: 0.8rem; font-weight: 600; transition: background 0.2s ease;
    }
    .navbar a.logout-btn:hover { background: #d1d5db; /* Darker hover background */ }
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
    .hero-section {
        text-align: center; padding: 5rem 2rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(240, 244, 248, 0.8) 100%), url('/assets/backgrounds/06.png'); /* Lighter gradient */
        background-size: cover; background-position: center;
    }
    .hero-section h1 { font-size: 2.8rem; font-weight: 800; color: #1f2937; /* Darker text */ margin-bottom: 0.5rem; }
    .hero-section p { font-size: 1.1rem; color: #4b5563; /* Darker text */ max-width: 700px; margin: 0 auto; }

    .content-area { padding: 3rem 2rem; max-width: 1200px; margin: 0 auto; }
    .section-title {
        font-size: 1.8rem; font-weight: 700; color: #1f2937; /* Darker text */
        margin-bottom: 1.5rem; border-bottom: 2px solid #2563eb; /* Blue border */
        padding-bottom: 0.5rem; display: inline-block;
    }
    .grid-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; }
    
    .action-card {
        background: #ffffff; /* White background */ border: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        border-radius: 0.75rem; padding: 2rem; text-align: center;
        display: flex; flex-direction: column; justify-content: space-between;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Lighter shadow */
    }
    .action-card h3 { font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem; }
    .action-card p { font-size: 0.95rem; color: #4b5563; margin-bottom: 1.5rem; }
    .btn {
        display: inline-flex; /* Changed to flex for centering */
        align-items: center; /* Center content vertically */
        justify-content: center; /* Center content horizontally */
        min-width: 180px; /* Increased min-width for this button */
        box-sizing: border-box; /* Include padding and border in the width */
        text-decoration: none; color: #ffffff;
        padding: 0.7rem 1.5rem; border-radius: 0.5rem; font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .btn-primary { background: linear-gradient(135deg, #38bdf8, #2563eb); } /* Blue primary button */
    .btn-secondary { background: linear-gradient(135deg, #10b981, #059669); } /* Green secondary button */
    .btn:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.15); }

    .event-card {
        background: #ffffff; /* White background */ border: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        border-radius: 0.75rem; padding: 1.5rem;
        display: flex; flex-direction: column;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Lighter shadow */
    }
    .event-card h3 { font-size: 1.2rem; font-weight: 600; color: #1f2937; }
    .event-card .date { font-size: 0.85rem; color: #4b5563; margin: 0.25rem 0 1rem 0; }
    .event-card p { font-size: 0.9rem; color: #4b5563; flex-grow: 1; margin-bottom: 1.5rem;}
    
    .table-container { overflow-x: auto; }
    .status-table {
        width: 100%; border-collapse: collapse; background: #ffffff; /* White background */
        border-radius: 0.75rem; overflow: hidden;
    }
    .status-table th, .status-table td { padding: 1rem 1.2rem; text-align: left; border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */ }
    .status-table th { font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.06em; color: #4b5563; /* Darker text */ }
    .status-table td { font-size: 0.95rem; }
    .status-badge {
        padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 600;
    }
</style>

<nav class="navbar">
    <a href="{{ route('homepage') }}" class="brand" style="text-decoration:none;">Sportify</a>
    <div class="user-info">
        <span>{{ auth()->user()->name }} (Committee)</span>
        <a href="{{ route('profile.show') }}" class="logout-btn" style="text-decoration: none; background: #e5e7eb;">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </div>
</nav>

<section class="hero-section">
    <h1>Committee Dashboard</h1>
    <p>Manage club events, review applications, and register for upcoming activities.</p>
</section>

<div class="content-area">
    <div class="grid-container" style="grid-template-columns: 1fr; gap: 2rem; margin-bottom: 3rem;">
        <div class="action-card">
            <h3>Create a New Event</h3>
            <p>Have an idea for a new tournament or activity? Submit your application for review.</p>
            <a href="#" class="btn btn-primary">Apply to Create Event</a>
        </div>
    </div>

    <div id="upcoming-events" style="margin-bottom: 3rem;">
        <h2 class="section-title">Upcoming Events</h2>
        <div class="grid-container">
            <div class="event-card">
                <h3>Annual Sports Gala</h3>
                <div class="date">Date: 2026-01-15</div>
                <p>A celebration of athletic achievements. We need committee members to help with logistics and awards.</p>
            </div>
            <div class="event-card">
                <h3>Inter-Club Basketball Tournament</h3>
                <div class="date">Date: 2026-02-20</div>
                <p>The most anticipated basketball tournament of the semester. Looking for scorekeepers and coordinators.</p>
            </div>
            <div class="event-card">
                <h3>Charity Fun Run (5k)</h3>
                <div class="date">Date: 2026-03-10</div>
                <p>A fun run to raise funds for a local charity. Volunteers needed for water stations and registration.</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="section-title">My Event Applications</h2>
        <div class="table-container">
            <table class="status-table">
                <thead>
                    <tr>
                        <th>Event Name</th>
                        <th>Submitted On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>University-Wide Chess Competition</td>
                        <td>2025-12-10</td>
                        <td><span class="status-badge" style="background:rgba(34,197,94,0.1);color:#16a34a;">Approved</span></td>
                    </tr>
                    <tr>
                        <td>Rock Climbing Workshop</td>
                        <td>2025-12-15</td>
                        <td><span class="status-badge" style="background:rgba(251,191,36,0.1);color:#b45309;">Pending</span></td>
                    </tr>
                    <tr>
                        <td>International Food & Sports Festival</td>
                        <td>2025-11-20</td>
                        <td><span class="status-badge" style="background:rgba(239,68,68,0.1);color:#b91c1c;">Rejected</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="main-footer">
    <div class="footer-links">
        <a href="{{ route('homepage') }}">Home</a>
        <a href="{{ route('about') }}">About Us</a>
    </div>
    &copy; {{ date('Y') }} Sportify. All Rights Reserved.
</footer>
@endsection