@extends('layouts.app')

@section('content')
    <div class="top-link">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="background:none;border:none;color:#9ca3af;cursor:pointer;font-size:0.8rem;padding:0;">
                Log out
            </button>
        </form>
    </div>

    <h1 style="display:flex;align-items:center;justify-content:space-between;gap:0.75rem;">
        <span>Admin Dashboard</span>
        <span style="font-size:0.8rem;color:#9ca3af;font-weight:500;">
            {{ auth()->user()->name }}
        </span>
    </h1>
    <p class="subtitle">
        Welcome to the admin control panel! Manage users, system settings, and oversee all activities.
    </p>

    @php
        $studentCount = \App\Models\User::where('role', 'student')->count();
        $committeeCount = \App\Models\User::where('role', 'committee')->count();
        $adminCount = \App\Models\User::where('role', 'admin')->count();
    @endphp

    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0.85rem;margin-top:1.25rem;margin-bottom:1.25rem;">
        <div style="padding:0.75rem 0.85rem;border-radius:0.75rem;background:rgba(15,23,42,0.9);border:1px solid rgba(148,163,184,0.3);">
            <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;margin-bottom:0.25rem;">
                Students
            </div>
            <div style="font-size:1.05rem;font-weight:600;color:#bbf7d0;">
                {{ $studentCount }}
            </div>
        </div>
        <div style="padding:0.75rem 0.85rem;border-radius:0.75rem;background:rgba(15,23,42,0.9);border:1px solid rgba(148,163,184,0.3);">
            <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;margin-bottom:0.25rem;">
                Committee
            </div>
            <div style="font-size:1.05rem;font-weight:600;color:#bae6fd;">
                {{ $committeeCount }}
            </div>
        </div>
        <div style="padding:0.75rem 0.85rem;border-radius:0.75rem;background:rgba(15,23,42,0.9);border:1px solid rgba(148,163,184,0.3);">
            <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;margin-bottom:0.25rem;">
                Admins
            </div>
            <div style="font-size:1.05rem;font-weight:600;color:#fecaca;">
                {{ $adminCount }}
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:0.75rem;">
        <p style="font-size:0.9rem;color:#d1d5db;">
            As an administrator, you have full access to manage users, create committee accounts, and oversee the entire system.
        </p>

        <div style="display:flex;flex-wrap:wrap;gap:0.6rem;margin-top:0.25rem;">
            <a href="{{ route('admin.users.index') }}" style="text-decoration:none;">
                <button type="button" style="border:none;border-radius:999px;padding:0.55rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:#ffffff;transition:transform 0.1s ease;">
                    User Management
                </button>
            </a>
            <a href="{{ route('admin.committee.create') }}" style="text-decoration:none;">
                <button type="button" style="border:none;border-radius:999px;padding:0.55rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#ffffff;transition:transform 0.1s ease;">
                    Create Committee Member
                </button>
            </a>
            <a href="{{ route('profile.show') }}" style="text-decoration:none;">
                <button type="button" style="border:none;border-radius:999px;padding:0.55rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;background:linear-gradient(135deg,#22c55e,#16a34a);color:#ffffff;transition:transform 0.1s ease;">
                    View Profile
                </button>
            </a>
            <button type="button" style="border:none;border-radius:999px;padding:0.55rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:not-allowed;background:rgba(15,23,42,0.9);color:#e5e7eb;border:1px solid rgba(148,163,184,0.6);">
                System Settings (soon)
            </button>
        </div>
    </div>
@endsection

