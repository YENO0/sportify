@extends('layouts.app')

@section('content')
    <div class="top-link">
        <a href="{{ route('homepage') }}">← Back to Homepage</a>
    </div>
    <h1>My Profile</h1>
    <p class="subtitle">View and manage your account information.</p>

    @if (session('success'))
        <div class="alert" style="background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.4); color: #bbf7d0; margin-bottom: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display: flex; align-items: center; margin-bottom: 1.5rem;">
        @if ($user->profile_picture)
            <img src="{{ asset('profile_pictures/' . $user->profile_picture) }}" alt="Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-right: 1.5rem;">
        @else
            <div style="width: 80px; height: 80px; border-radius: 50%; background-color: #334155; display: flex; align-items: center; justify-content: center; margin-right: 1.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        @endif
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 600; color: #e5e7eb;">{{ $user->name }}</h2>
            <p style="font-size: 1rem; color: #9ca3af;">{{ $user->email }}</p>
        </div>
    </div>

    <!-- Profile Information -->
    <div style="background:rgba(15,23,42,0.9);border:1px solid rgba(148,163,184,0.3);border-radius:0.75rem;padding:1.5rem;margin-bottom:1rem;">
        <div style="display:grid;gap:1rem;">
            <div>
                <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;margin-bottom:0.35rem;">
                    Name
                </div>
                <div style="font-size:1rem;color:#e5e7eb;font-weight:500;">
                    {{ $user->name }}
                </div>
            </div>

            <div>
                <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;margin-bottom:0.35rem;">
                    Email
                </div>
                <div style="font-size:1rem;color:#e5e7eb;font-weight:500;">
                    {{ $user->email }}
                </div>
            </div>

            <div>
                <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;margin-bottom:0.35rem;">
                    Role
                </div>
                <div>
                    <span style="display:inline-block;padding:0.35rem 0.75rem;border-radius:0.5rem;font-size:0.85rem;font-weight:600;text-transform:capitalize;
                        @if($user->role === 'student') background:rgba(34,197,94,0.2);color:#bbf7d0;
                        @elseif($user->role === 'committee') background:rgba(59,130,246,0.2);color:#bae6fd;
                        @else background:rgba(239,68,68,0.2);color:#fecaca;
                        @endif">
                        {{ $user->role }}
                    </span>
                </div>
            </div>

            <div>
                <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;margin-bottom:0.35rem;">
                    Member Since
                </div>
                <div style="font-size:0.9rem;color:#9ca3af;">
                    {{ $user->created_at->format('F d, Y') }}
                </div>
            </div>

            @if($user->email_verified_at)
                <div>
                    <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;margin-bottom:0.35rem;">
                        Email Verified
                    </div>
                    <div style="font-size:0.9rem;color:#bbf7d0;">
                        ✓ Verified on {{ $user->email_verified_at->format('M d, Y') }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Buttons -->
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
        <a href="{{ route('profile.edit') }}" style="text-decoration:none;">
            <button type="button" style="border:none;border-radius:0.5rem;padding:0.7rem 1.25rem;font-size:0.9rem;font-weight:600;cursor:pointer;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#ffffff;transition:transform 0.1s ease;">
                Edit Profile
            </button>
        </a>
    </div>

    <p class="muted-link" style="margin-top:1.5rem;">
        <a href="{{ route('homepage') }}">Return to homepage</a>
    </p>
@endsection




