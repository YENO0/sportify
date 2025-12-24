@extends('layouts.app')

@section('content')
<style>
    /* Full-width layout overrides */
    body { display: block; }
    .card {
        max-width: none; width: 100%; min-height: 100vh;
        border-radius: 0; padding: 0; background: #f8fafc; /* Light background */
        border: none; box-shadow: none;
    }

    /* Re-usable website styles */
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

    /* Page Specific Styles */
    .page-header {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(240, 244, 248, 0.8) 100%), url(/assets/backgrounds/02.png); /* Lighter gradient */
        background-size: cover; background-position: center;
        padding: 4rem 2rem;
        text-align: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
    }
    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937; /* Darker text */
    }
    .content-area {
        padding: 3rem 2rem;
        max-width: 900px;
        margin: 0 auto;
        color: #4b5563; /* Darker text */
        line-height: 1.7;
    }
    .content-area h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1f2937; /* Darker text */
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #2563eb; /* Blue border */
        padding-bottom: 0.5rem;
    }
    .content-area p {
        margin-bottom: 1.5rem;
    }
</style>

<nav class="navbar">
    <a href="{{ route('homepage') }}" class="brand" style="text-decoration:none;">Sportify</a>
    <div class="user-info">
        @auth
            <span>Welcome, {{ auth()->user()->name }}</span>
            <a href="{{ route('profile.show') }}" class="logout-btn" style="text-decoration: none; background: #e5e7eb;">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">Log Out</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="logout-btn" style="text-decoration: none;">Login</a>
            <a href="{{ route('register') }}" class="logout-btn" style="text-decoration: none; background-color: #10b981;">Register</a>
        @endauth
    </div>
</nav>

<header class="page-header">
    <h1>About Sportify</h1>
</header>

<main class="content-area">
    <h2>Our Mission</h2>
    <p>
        At Sportify, our mission is to streamline the management of sports clubs and events to foster a more vibrant, engaged, and organized sporting community. We believe that passion for sports should be matched with technology that simplifies coordination, enhances participation, and celebrates every achievement. Our platform is designed to empower students, committee members, and administrators alike, making sports management seamless and efficient.
    </p>

    <h2>What We Do</h2>
    <p>
        Sportify provides a centralized hub for managing all aspects of your sports club. From event scheduling and promotion to tracking member activities and achievements, our system is built to handle it all. We aim to reduce the administrative burden so that you can focus on what truly matters: the love of the game.
    </p>
    
    <h2>Our Vision</h2>
    <p>
        We envision a future where every sports club, regardless of size, has access to powerful and intuitive tools to thrive. By connecting athletes, organizers, and fans, we hope to build a stronger, more connected sporting world, one club at a time.
    </p>
</main>

<footer class="main-footer">
    <div class="footer-links">
        <a href="{{ route('homepage') }}">Home</a>
        <a href="{{ route('about') }}">About Us</a>
    </div>
    &copy; {{ date('Y') }} Sportify. All Rights Reserved.
</footer>
@endsection
