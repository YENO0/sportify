@extends('layouts.app')

@section('content')
<style>
    /* Full-width layout overrides */
    body { display: block; }
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

    /* Profile Page Specific Styles */
    .profile-header {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(240, 244, 248, 0.8) 100%), url(/assets/backgrounds/08.png); /* Lighter gradient */
        background-size: cover; background-position: center;
        padding: 4rem 2rem;
        text-align: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
    }
    .profile-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1f2937; /* Darker text */
    }
    .content-area {
        padding: 3rem 2rem;
        max-width: 900px;
        margin: 0 auto;
    }
    .profile-container {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 3rem;
    }
    .profile-sidebar .profile-picture {
        width: 200px;
        height: 200px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #d1d5db; /* Lighter border */
        margin-bottom: 1rem;
    }
    .profile-sidebar .profile-name {
        font-size: 1.8rem;
        font-weight: 600;
        color: #1f2937; /* Darker text */
        margin-bottom: 0.25rem;
    }
    .profile-sidebar .profile-email {
        font-size: 1rem;
        color: #4b5563; /* Darker text */
        margin-bottom: 1.5rem;
    }
    .profile-sidebar .edit-btn {
        display: block; text-align: center; text-decoration: none;
        background: linear-gradient(135deg,#2563eb,#1d4ed8); /* Blue gradient */
        color: #ffffff;
        padding: 0.7rem 1.25rem; font-size: 0.9rem; font-weight: 600;
        border-radius: 0.5rem; transition: transform 0.1s ease;
    }
    .profile-sidebar .edit-btn:hover { transform: translateY(-2px); }

    .profile-main .info-group {
        background: #ffffff; /* White background */
        border: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        border-radius: 0.75rem;
        padding: 2rem;
    }
    .profile-main .info-item { margin-bottom: 1.5rem; }
    .profile-main .info-item:last-child { margin-bottom: 0; }
    .profile-main .info-label {
        font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.08em;
        color: #4b5563; /* Darker text */
        font-weight: 600; margin-bottom: 0.5rem;
    }
    .profile-main .info-value {
        font-size: 1rem; color: #1f2937; /* Darker text */
        font-weight: 500;
    }
    .profile-main .info-value .role-badge {
        display: inline-block; padding: 0.35rem 0.75rem; border-radius: 0.5rem;
        font-size: 0.85rem; font-weight: 600; text-transform: capitalize;
    }
    .alert-success {
        background: rgba(220, 252, 231, 1); /* Light green background */
        border: 1px solid rgba(134, 239, 172, 1); /* Green border */
        color: #065f46; /* Dark green text */
        padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;
    }
</style>

<header class="profile-header">
    <h1>My Profile</h1>
</header>

<main class="content-area">
    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="profile-container">
        <aside class="profile-sidebar">
            @if ($user->profile_picture)
                <img src="{{ asset('profile_pictures/' . $user->profile_picture) }}" alt="Profile Picture" class="profile-picture">
            @else
                <div style="width: 200px; height: 200px; border-radius: 50%; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center; border: 4px solid #cbd5e1; margin-bottom: 1rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" style="color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            @endif
            <h2 class="profile-name">{{ $user->name }}</h2>
            <p class="profile-email">{{ $user->email }}</p>
            <a href="{{ route('profile.edit') }}" class="edit-btn">Edit Profile</a>
        </aside>

        <section class="profile-main">
            <div class="info-group">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">{{ $user->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email Address</div>
                    <div class="info-value">{{ $user->email }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Gender</div>
                    <div class="info-value">{{ $user->gender ?? 'Not specified' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Birthday</div>
                    <div class="info-value">{{ $user->birthday ? $user->birthday->format('F d, Y') : 'Not specified' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Contact</div>
                    <div class="info-value">{{ $user->contact ?? 'Not specified' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Role</div>
                    <div class="info-value">
                        <span class="role-badge" style="
                            @if($user->role === 'student') background:rgba(34,197,94,0.1);color:#16a34a;
                            @elseif($user->role === 'committee') background:rgba(59,130,246,0.1);color:#2563eb;
                            @else background:rgba(239,68,68,0.1);color:#b91c1c;
                            @endif">
                            {{ $user->role }}
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Member Since</div>
                    <div class="info-value">{{ $user->created_at->format('F d, Y') }}</div>
                </div>
                @if($user->email_verified_at)
                    <div class="info-item">
                        <div class="info-label">Email Status</div>
                        <div class="info-value" style="color: #16a34a;">✓ Verified</div>
                    </div>
                @endif
            </div>
        </section>
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