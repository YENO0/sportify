@extends('layouts.app')

@section('content')
<style>
    /* Override the default layout to go full-width */
    body {
        display: block; /* Remove flex centering */
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
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        align-items: stretch;
    }

    /* Make the whole card clickable and equal-height per grid row */
    .grid-container > a {
        display: block;
        height: 100%;
    }

    @media (max-width: 1200px) {
        .grid-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 900px) {
        .grid-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
        .grid-container {
            grid-template-columns: 1fr;
        }
    }
    .info-card {
        background: #ffffff; /* White background */
        border: 1px solid transparent; /* no visible border until hover */
        padding: 0;
        border-radius: 0.75rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        box-shadow: none; /* shadow only on hover */
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%; /* fill grid row */
        min-height: 300px; /* keep all cards consistent size */
    }
    .info-card:hover {
        transform: translateY(-5px);
        border-color: rgba(0, 0, 0, 0.10);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15); /* show shadow on hover */
    }
    .event-image {
        width: 100%;
        height: 170px;
        object-fit: cover;
        background: #f3f4f6;
        display: block;
    }
    .event-image-placeholder {
        width: 100%;
        height: 170px;
        background: #f3f4f6;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    }
    .card-body {
        padding:0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
        justify-content: flex-start; /* align content to top */
    }
    .info-card h3 {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1f2937; /* Darker text */
        margin: 0;
        line-height: 1.3;
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        line-clamp: 2;
    }
    .info-card .date {
        font-size: 0.8rem;
        color: #4b5563; /* Darker text */
        margin: 0;
    }
    .info-card p {
        font-size: 0.9rem;
        color: #4b5563; /* Darker text */
        margin: 0;
        overflow: hidden;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 4;
        line-clamp: 4;
    }
    .empty-message {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem;
        padding: 1.25rem 1.5rem;
        color: #4b5563;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
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

<section class="hero-section">
    <h1>Your Sporting Journey Starts Here</h1>
    <p>Welcome back to your personal dashboard. Track your progress, discover new events, and connect with the sports community.</p>
</section>

<div class="content-area">
    <h2 class="section-title">Upcoming Events</h2>
    @if(($upcomingEvents ?? collect())->count() > 0)
        <div class="grid-container">
            @foreach($upcomingEvents as $event)
                <a href="{{ route('events.show', $event) }}" style="text-decoration:none;color:inherit;">
                    <div class="info-card">
                        @if(!empty($event->event_poster))
                            <img class="event-image" src="{{ asset('storage/' . $event->event_poster) }}" alt="{{ $event->event_name }}">
                        @else
                            <div class="event-image-placeholder">No Image</div>
                        @endif
                        <div class="card-body">
                            <h3>{{ $event->event_name }}</h3>
                            <div class="date">
                                Date:
                                {{ \Carbon\Carbon::parse($event->event_start_date)->format('Y-m-d') }}
                                @if(!empty($event->event_end_date))
                                    - {{ \Carbon\Carbon::parse($event->event_end_date)->format('Y-m-d') }}
                                @endif
                            </div>
                            <p>{{ \Illuminate\Support\Str::words(strip_tags($event->event_description ?? ''), 30, ' ...') }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-message">No upcoming events at the moment.</div>
    @endif
</div>

<div class="content-area" style="padding-top: 0;">
    <h2 class="section-title">Recent Events</h2>
    @if(($recentEvents ?? collect())->count() > 0)
        <div class="grid-container">
            @foreach($recentEvents as $event)
                <a href="{{ route('events.show', $event) }}" style="text-decoration:none;color:inherit;">
                    <div class="info-card">
                        @if(!empty($event->event_poster))
                            <img class="event-image" src="{{ asset('storage/' . $event->event_poster) }}" alt="{{ $event->event_name }}">
                        @else
                            <div class="event-image-placeholder">No Image</div>
                        @endif
                        <div class="card-body">
                            <h3>{{ $event->event_name }}</h3>
                            <div class="date">
                                Date:
                                {{ \Carbon\Carbon::parse($event->event_start_date)->format('Y-m-d') }}
                                @if(!empty($event->event_end_date))
                                    - {{ \Carbon\Carbon::parse($event->event_end_date)->format('Y-m-d') }}
                                @endif
                            </div>
                            <p>{{ \Illuminate\Support\Str::words(strip_tags($event->event_description ?? ''), 30, ' ...') }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="empty-message">No recent events in the past month.</div>
    @endif
</div>

<footer class="main-footer">
    <div class="footer-links">
        <a href="{{ route('homepage') }}">Home</a>
        <a href="{{ route('about') }}">About Us</a>
    </div>
    &copy; {{ date('Y') }} Sportify. All Rights Reserved.
</footer>
@endsection
