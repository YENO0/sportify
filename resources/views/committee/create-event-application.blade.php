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
    .page-header {
        text-align: center; padding: 4rem 2rem;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(240, 244, 248, 0.8) 100%), url('/assets/backgrounds/07.png'); /* Lighter gradient */
        background-size: cover; background-position: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
    }
    .page-header h1 { font-size: 2.5rem; font-weight: 800; color: #1f2937; /* Darker text */ margin-bottom: 0.5rem; }
    .page-header p { font-size: 1.1rem; color: #4b5563; /* Darker text */ max-width: 700px; margin: 0 auto; }

    .content-area { padding: 3rem 2rem; max-width: 700px; margin: 0 auto; }
    .form-container {
        background: #ffffff; /* White background */ border: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        border-radius: 0.75rem; padding: 2rem;
    }
    .field { margin-bottom: 1.5rem; }
    label {
        display: block; font-size: 0.8rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #374151; /* Darker label */ margin-bottom: 0.5rem;
    }
    input[type="text"], input[type="date"], input[type="number"], textarea {
        width: 100%; padding: 0.7rem 0.85rem; border-radius: 0.6rem;
        border: 1px solid rgba(209, 213, 219, 1); /* Light border */ background: #f9fafb; /* Lighter input background */
        color: #1f2937; /* Darker input text */ font-size: 0.95rem; outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }
    input:focus, textarea:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.3); /* Lighter focus shadow */
        background: #ffffff; /* White on focus */
    }
    textarea { min-height: 100px; resize: vertical; }
    .button-group { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;}
    .btn {
        display: inline-block; text-decoration: none; color: #ffffff;
        padding: 0.7rem 1.5rem; border-radius: 0.5rem; font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none; cursor: pointer;
    }
    .btn-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); } /* Blue primary button */
    .btn-secondary { background: #e5e7eb; color: #374151; } /* Light secondary button */
    .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }

    .alert-danger {
        background: rgba(254, 226, 226, 1); /* Light red background */ border: 1px solid rgba(252, 165, 165, 1); /* Red border */
        color: #991b1b; /* Dark red text */ padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;
    }
    .alert-danger ul { margin: 0; padding-left: 1.2rem; }
</style>

<nav class="navbar">
    <a href="{{ route('homepage') }}" class="brand" style="text-decoration:none;">Sportify</a>
    <div class="user-info">
        <span>Welcome, {{ auth()->user()->name }}</span>
        <a href="{{ route('profile.show') }}" class="logout-btn" style="text-decoration: none; background: #e5e7eb;">Profile</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">Log Out</button>
        </form>
    </div>
</nav>

<header class="page-header">
    <h1>Apply to Create Event</h1>
    <p>Submit your proposal for a new event. Our administrators will review your application.</p>
</header>

<main class="content-area">
    @if ($errors->any())
        <div class="alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="form-container">
        <form method="POST" action="{{ route('committee.event-applications.store') }}">
            @csrf
            <div class="field">
                <label for="event_name">Event Name</label>
                <input type="text" id="event_name" name="event_name" value="{{ old('event_name') }}" required>
            </div>

            <div class="field">
                <label for="description">Description</label>
                <textarea id="description" name="description" required>{{ old('description') }}</textarea>
            </div>

            <div class="field">
                <label for="event_date">Proposed Date</label>
                <input type="date" id="event_date" name="event_date" value="{{ old('event_date') }}" required>
            </div>

            <div class="field">
                <label for="proposed_budget">Proposed Budget ($)</label>
                <input type="number" id="proposed_budget" name="proposed_budget" value="{{ old('proposed_budget') }}" min="0" step="1" required>
            </div>

            <div class="button-group">
                <a href="{{ route('homepage') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </div>
        </form>
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
