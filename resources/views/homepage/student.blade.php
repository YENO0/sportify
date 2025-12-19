@extends('layouts.app')

@section('content')
<style>
    /* Override the default layout to go full-width */
    body {
        display: block; /* Remove flex centering */
    }
    .card {
        max-width: none;
        width: 100%;
        min-height: 100vh;
        border-radius: 0;
        padding: 0;
        background: #f8fafc; /* Light background */
        border: none;
        box-shadow: none;
    }

    /* New Styles for the website look */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        background: rgba(255, 255, 255, 0.8); /* Lighter navbar background */
        border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        position: sticky;
        top: 0;
        z-index: 10;
        backdrop-filter: blur(8px);
    }
    .navbar .brand {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937; /* Darker text */
    }
    .navbar .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .navbar .user-info span {
        font-size: 0.9rem;
        color: #4b5563; /* Darker text */
    }
    .navbar .logout-btn {
        background: #e5e7eb; /* Light background */
        border: none;
        color: #374151; /* Darker text */
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        cursor: pointer;
        font-size: 0.8rem;
        font-weight: 600;
        transition: background 0.2s ease;
    }
    .navbar .logout-btn:hover {
        background: #d1d5db; /* Darker hover background */
    }

    .hero-section {
        text-align: center;
        padding: 6rem 2rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(240, 244, 248, 0.8) 100%), url('/assets/backgrounds/04.png'); /* Lighter gradient */
        background-size: cover;
        background-position: center;
    }
    .hero-section h1 {
        font-size: 3rem;
        font-weight: 800;
        color: #1f2937; /* Darker text */
        margin-bottom: 1rem;
    }
    .hero-section p {
        font-size: 1.1rem;
        color: #4b5563; /* Darker text */
        max-width: 600px;
        margin: 0 auto;
    }

    .content-area {
        padding: 3rem 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    .section-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1f2937; /* Darker text */
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #2563eb; /* Blue border */
        padding-bottom: 0.5rem;
        display: inline-block;
    }
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .info-card {
        background: #ffffff; /* White background */
        border: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        padding: 1.5rem;
        border-radius: 0.75rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Lighter shadow */
    }
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15); /* Lighter hover shadow */
    }
    .info-card h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1f2937; /* Darker text */
        margin-bottom: 0.5rem;
    }
    .info-card .date {
        font-size: 0.8rem;
        color: #4b5563; /* Darker text */
        margin-bottom: 1rem;
    }
    .info-card p {
        font-size: 0.9rem;
        color: #4b5563; /* Darker text */
    }
    .activity-item {
         background: #ffffff; /* White background */
        border: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        padding: 1.2rem;
        border-radius: 0.75rem;
        font-size: 0.9rem;
        color: #4b5563; /* Darker text */
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Lighter shadow */
    }
     .activity-item .time {
        font-size: 0.8rem;
        color: #4b5563; /* Darker text */
        margin-top: 0.25rem;
     }

    .main-footer {
        text-align: center;
        padding: 2rem;
        margin-top: 2rem;
        background: #f0f4f8; /* Light grey background */
        border-top: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        font-size: 0.9rem;
        color: #4b5563; /* Darker text */
    }
    .footer-links {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }
    .footer-links a {
        color: #4b5563; /* Darker text */
        text-decoration: none;
        font-size: 0.9rem;
    }
    .footer-links a:hover {
        color: #1f2937; /* Even darker on hover */
        text-decoration: underline;
    }

</style>

<nav class="navbar">
    <div class="brand">Sportify</div>
    <div class="user-info">
        <span>Welcome, {{ auth()->user()->name }}</span>
        <a href="{{ route('profile.show') }}" class="logout-btn" style="text-decoration: none; background: #e5e7eb;">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </div>
</nav>

<section class="hero-section">
    <h1>Your Sporting Journey Starts Here</h1>
    <p>Welcome back to your personal dashboard. Track your progress, discover new events, and connect with the sports community.</p>
</section>

<div class="content-area">
    <h2 class="section-title">Upcoming Events</h2>
    <div class="grid-container">
        <div class="info-card">
            <h3>Annual Sports Gala</h3>
            <div class="date">Date: 2026-01-15</div>
            <p>A celebration of athletic achievements throughout the year. Don't miss out!</p>
        </div>
        <div class="info-card">
            <h3>Inter-Club Basketball Tournament</h3>
            <div class="date">Date: 2026-02-20</div>
            <p>The most anticipated basketball tournament of the semester. Register your team now!</p>
        </div>
        <div class="info-card">
            <h3>Friendly Football Match</h3>
            <div class="date">Date: 2026-02-22</div>
            <p>A casual football match between students. All skill levels are welcome!</p>
        </div>
    </div>
</div>

<div class="content-area" style="padding-top: 0;">
    <h2 class="section-title">Recent Activities</h2>
    <div class="grid-container" style="gap: 1rem;">
        <div class="activity-item">
            <p>Logged 2 hours of basketball practice.</p>
            <div class="time">2 days ago</div>
        </div>
        <div class="activity-item">
            <p>Attended the weekly sports committee meeting.</p>
            <div class="time">5 days ago</div>
        </div>
         <div class="activity-item">
            <p>Updated profile with new achievements.</p>
            <div class="time">1 week ago</div>
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
