<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Sportify</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0f172a;
            color: #e5e7eb;
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: #020617;
            border-radius: 1rem;
            padding: 2rem 2.5rem;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }
        .card h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        .card p.subtitle {
            font-size: 0.9rem;
            color: #9ca3af;
            margin-bottom: 1.5rem;
        }
        .top-link {
            text-align: right;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }
        .top-link a {
            color: #9ca3af;
            text-decoration: none;
        }
        .top-link a:hover {
            color: #e5e7eb;
        }
        .alert {
            padding: 0.65rem 0.75rem;
            border-radius: 0.6rem;
            font-size: 0.8rem;
            margin-bottom: 0.9rem;
        }
        .alert-danger {
            background: rgba(248, 113, 113, 0.08);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: #fecaca;
        }
        .muted-link {
            font-size: 0.85rem;
            color: #9ca3af;
            text-align: center;
            margin-top: 1rem;
        }
        .muted-link a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 500;
        }
        .muted-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .card {
                padding: 1.5rem;
            }
            table {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
    <div class="top-link">
        <a href="{{ route('homepage') }}">← Back to Homepage</a>
    </div>
    <h1>User Management</h1>
    <p class="subtitle">Manage students and committee members. View, create, edit, or delete user accounts.</p>

    @if (session('success'))
        <div class="alert" style="background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.4); color: #bbf7d0; margin-bottom: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Statistics -->
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:0.85rem;margin-bottom:1.5rem;">
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

    <!-- Filter and Create Button -->
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:0.75rem;">
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
            <a href="{{ route('admin.users.index', ['role' => 'all']) }}" 
               style="text-decoration:none;padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.85rem;font-weight:600;background:{{ $role === 'all' ? 'rgba(59,130,246,0.2)' : 'rgba(15,23,42,0.9)' }};color:{{ $role === 'all' ? '#93c5fd' : '#9ca3af' }};border:1px solid rgba(148,163,184,0.3);">
                All
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'student']) }}" 
               style="text-decoration:none;padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.85rem;font-weight:600;background:{{ $role === 'student' ? 'rgba(34,197,94,0.2)' : 'rgba(15,23,42,0.9)' }};color:{{ $role === 'student' ? '#bbf7d0' : '#9ca3af' }};border:1px solid rgba(148,163,184,0.3);">
                Students
            </a>
            <a href="{{ route('admin.users.index', ['role' => 'committee']) }}" 
               style="text-decoration:none;padding:0.5rem 1rem;border-radius:0.5rem;font-size:0.85rem;font-weight:600;background:{{ $role === 'committee' ? 'rgba(59,130,246,0.2)' : 'rgba(15,23,42,0.9)' }};color:{{ $role === 'committee' ? '#bae6fd' : '#9ca3af' }};border:1px solid rgba(148,163,184,0.3);">
                Committee
            </a>
        </div>
        <a href="{{ route('admin.users.create') }}" style="text-decoration:none;">
            <button type="button" style="border:none;border-radius:0.5rem;padding:0.55rem 1.1rem;font-size:0.85rem;font-weight:600;cursor:pointer;background:linear-gradient(135deg,#22c55e,#16a34a);color:#ffffff;">
                + Create User
            </button>
        </a>
    </div>

    <!-- Users Table -->
    <div style="background:rgba(15,23,42,0.9);border:1px solid rgba(148,163,184,0.3);border-radius:0.75rem;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:rgba(15,23,42,1);border-bottom:1px solid rgba(148,163,184,0.3);">
                    <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;">Name</th>
                    <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;">Email</th>
                    <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;">Role</th>
                    <th style="padding:0.75rem 1rem;text-align:left;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;">Created</th>
                    <th style="padding:0.75rem 1rem;text-align:right;font-size:0.75rem;text-transform:uppercase;letter-spacing:0.08em;color:#9ca3af;font-weight:600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom:1px solid rgba(148,163,184,0.1);">
                        <td style="padding:0.75rem 1rem;color:#e5e7eb;font-size:0.9rem;">{{ $user->name }}</td>
                        <td style="padding:0.75rem 1rem;color:#9ca3af;font-size:0.85rem;">{{ $user->email }}</td>
                        <td style="padding:0.75rem 1rem;">
                            <span style="display:inline-block;padding:0.25rem 0.6rem;border-radius:0.375rem;font-size:0.75rem;font-weight:600;text-transform:capitalize;
                                @if($user->role === 'student') background:rgba(34,197,94,0.2);color:#bbf7d0;
                                @elseif($user->role === 'committee') background:rgba(59,130,246,0.2);color:#bae6fd;
                                @else background:rgba(239,68,68,0.2);color:#fecaca;
                                @endif">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td style="padding:0.75rem 1rem;color:#9ca3af;font-size:0.8rem;">{{ $user->created_at->format('M d, Y') }}</td>
                        <td style="padding:0.75rem 1rem;text-align:right;">
                            <div style="display:flex;gap:0.5rem;justify-content:flex-end;">
                                @if(!$user->isAdmin())
                                    <a href="{{ route('admin.users.edit', $user) }}" style="text-decoration:none;padding:0.35rem 0.7rem;border-radius:0.375rem;font-size:0.8rem;font-weight:600;background:rgba(59,130,246,0.2);color:#bae6fd;border:1px solid rgba(59,130,246,0.3);">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="border:none;padding:0.35rem 0.7rem;border-radius:0.375rem;font-size:0.8rem;font-weight:600;background:rgba(239,68,68,0.2);color:#fecaca;border:1px solid rgba(239,68,68,0.3);cursor:pointer;">
                                            Delete
                                        </button>
                                    </form>
                                @else
                                    <span style="color:#9ca3af;font-size:0.8rem;">Protected</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:2rem;text-align:center;color:#9ca3af;">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div style="margin-top:1.5rem;display:flex;justify-content:center;gap:0.5rem;">
            @if($users->onFirstPage())
                <span style="padding:0.5rem 1rem;border-radius:0.5rem;background:rgba(15,23,42,0.9);color:#6b7280;border:1px solid rgba(148,163,184,0.3);">Previous</span>
            @else
                <a href="{{ $users->previousPageUrl() }}" style="text-decoration:none;padding:0.5rem 1rem;border-radius:0.5rem;background:rgba(15,23,42,0.9);color:#9ca3af;border:1px solid rgba(148,163,184,0.3);">Previous</a>
            @endif

            <span style="padding:0.5rem 1rem;color:#9ca3af;font-size:0.85rem;">
                Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
            </span>

            @if($users->hasMorePages())
                <a href="{{ $users->nextPageUrl() }}" style="text-decoration:none;padding:0.5rem 1rem;border-radius:0.5rem;background:rgba(15,23,42,0.9);color:#9ca3af;border:1px solid rgba(148,163,184,0.3);">Next</a>
            @else
                <span style="padding:0.5rem 1rem;border-radius:0.5rem;background:rgba(15,23,42,0.9);color:#6b7280;border:1px solid rgba(148,163,184,0.3);">Next</span>
            @endif
        </div>
    @endif

    <p class="muted-link" style="margin-top:1.5rem;">
        <a href="{{ route('homepage') }}">Return to homepage</a>
    </p>
        </div>
    </div>
</body>
</html>

