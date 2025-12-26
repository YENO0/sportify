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
    .stat-card.red .value { color: #be123c; } /* Specific color for admin count */

    .alert {
        padding: 0.8rem 1rem; border-radius: 0.5rem; font-size: 0.9rem; margin-bottom: 1.5rem;
        border: 1px solid;
    }
    .alert-success { background: #dcfce7; border-color: #4ade80; color: #16a34a; }
    .alert-error { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }

    .filter-options a {
        text-decoration: none; padding: 0.5rem 1rem; border-radius: 0.5rem;
        font-size: 0.85rem; font-weight: 600; border: 1px solid rgba(0, 0, 0, 0.1);
        color: #4b5563; background: #f9fafb;
    }
    .filter-options a.active {
        background: #e0e7ed; border-color: #94a3b8; color: #1f2937;
    }
    .filter-options a:hover { background: #e5e7eb; }

    .btn-create-user {
        border:none;border-radius:0.5rem;padding:0.55rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;
        background:linear-gradient(135deg,#22c55e,#16a34a);color:#ffffff;
        transition: transform 0.1s ease;
    }
    .btn-create-user:hover { transform: translateY(-2px); }

    .users-table-container {
        background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem; overflow: hidden;
    }
    .users-table { width:100%;border-collapse:collapse; }
    .users-table thead tr { background:#f9fafb;border-bottom:1px solid rgba(0,0,0,0.1); }
    .users-table th, .users-table td { padding:0.75rem 1rem;text-align:left; }
    .users-table th { font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#4b5563;font-weight:600; }
    .users-table td { color:#374151;font-size:0.9rem; }
    .users-table tbody tr { border-bottom:1px solid rgba(0,0,0,0.05); }
    .users-table tbody tr:last-child { border-bottom:none; }
    
    .role-badge {
        display:inline-block;padding:0.25rem 0.6rem;border-radius:0.375rem;
        font-size:0.75rem;font-weight:600;text-transform:capitalize;
    }
    /* Specific badge colors for light theme */
    .role-badge.student { background:rgba(34,197,94,0.1);color:#16a34a; }
    .role-badge.committee { background:rgba(59,130,246,0.1);color:#2563eb; }
    .role-badge.admin { background:rgba(239,68,68,0.1);color:#dc2626; }

    .action-buttons { display:flex;gap:0.5rem;justify-content:flex-end; }
    .action-buttons a, .action-buttons button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 70px; /* Adjusted for consistent size */
        box-sizing: border-box; /* Include padding and border in the width */
        text-decoration:none;padding:0.35rem 0.7rem;border-radius:0.375rem;
        font-size:0.8rem;font-weight:600;border:1px solid;cursor:pointer;
        transition: background 0.1s ease;
    }
    .action-buttons a.edit-btn { background:rgba(59,130,246,0.1);color:#2563eb;border-color:rgba(59,130,246,0.3); }
    .action-buttons a.edit-btn:hover { background:rgba(59,130,246,0.2); }
    .action-buttons button.delete-btn { background:rgba(239,68,68,0.1);color:#dc2626;border-color:rgba(239,68,68,0.3); }
    .action-buttons button.delete-btn:hover { background:rgba(239,68,68,0.2); }
    .action-buttons .protected-text { color:#6b7280;font-size:0.8rem; }

    .pagination-container { margin-top:1.5rem;display:flex;justify-content:center;gap:0.5rem; }
    .pagination-container span, .pagination-container a {
        padding:0.5rem 1rem;border-radius:0.5rem;border:1px solid rgba(0,0,0,0.1);
        font-size:0.85rem;text-decoration:none;
    }
    .pagination-container span { background:#f9fafb;color:#6b7280; }
    .pagination-container a { background:#ffffff;color:#4b5563; }
    .pagination-container a:hover { background:#f0f4f8; }

    /* Media Queries */
    @media (max-width: 768px) {
        .content-area { padding: 1.5rem; }
        .page-header { padding: 1.5rem; }
        .users-table th, .users-table td { padding: 0.6rem 0.8rem; font-size: 0.8rem; }
        .stat-card .value { font-size: 1.8rem; }
        .section-title { font-size: 1.3rem; }
    }
</style>

<header class="page-header">
    <h1>User Management</h1>
    <p>Manage all user accounts, roles, and permissions.</p>
</header>

<main class="content-area">
    <!-- Statistics -->
    <div class="grid-container" style="margin-bottom: 2.5rem;">
        <div class="stat-card">
            <div class="label">Students</div>
            <div class="value" style="color: #16a34a;">{{ $studentCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Committee</div>
            <div class="value" style="color: #2563eb;">{{ $committeeCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Admins</div>
            <div class="value stat-card.red" style="color: #dc2626;">{{ $adminCount }}</div>
        </div>
        <div class="stat-card">
            <div class="label">Total Users</div>
            <div class="value" style="color: #4b5563;">{{ $totalUsers }}</div>
        </div>
    </div>

    <!-- Filter and Create Button -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
        <div class="filter-options" style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <a href="{{ route('admin.users.index', ['role' => 'all']) }}" 
               class="{{ $role === 'all' ? 'active' : '' }}">
                All
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'student']) }}" 
               class="{{ $role === 'student' ? 'active' : '' }}">
                Students
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'committee']) }}" 
               class="{{ $role === 'committee' ? 'active' : '' }}">
                Committee
            </a>
        </div>
        <a href="{{ route('admin.users.create') }}" style="text-decoration:none;">
            <button type="button" class="btn-create-user">
                + Create User
            </button>
        </a>
    </div>

    <!-- Users Table -->
    <div class="users-table-container">
        <table class="users-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="role-badge {{ $user->role }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>{{ $user->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="action-buttons">
                                @if(!$user->isAdmin())
                                    <a href="{{ route('admin.users.edit', $user) }}" class="edit-btn">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="protected-text">Protected</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:2rem;text-align:center;color:#6b7280;">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="pagination-container">
            @if($users->onFirstPage())
                <span>Previous</span>
            @else
                <a href="{{ $users->previousPageUrl() }}">Previous</a>
            @endif

            <span>
                Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
            </span>

            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}">Next</a>
            @else
                <span>Next</span>
            @endif
        </div>
    @endif
</main>

<footer class="main-footer">
    <div class="footer-links">
        <a href="{{ route('homepage') }}">Home</a>
        <a href="{{ route('about') }}">About Us</a>
    </div>
    &copy; {{ date('Y') }} Sportify. All Rights Reserved.
</footer>
@endsection