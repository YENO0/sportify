<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sportify - Inventory Management')</title>
    
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: rgba(255, 255, 255, 0.8);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(8px);
        }
        .navbar .brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            text-decoration: none;
        }
        .navbar .nav-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 1.25rem;
            flex: 1;
            min-width: 0;
        }
        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .navbar .nav-links a {
            color: #4b5563;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }
        .navbar .nav-links a:hover {
            background: #f3f4f6;
            color: #1f2937;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .navbar .user-info span {
            font-size: 0.9rem;
            color: #4b5563;
        }
        .navbar .logout-btn {
            background: #e5e7eb;
            border: none;
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 600;
            transition: background 0.2s ease;
            text-decoration: none;
            display: inline-block;
        }
        .navbar .logout-btn:hover {
            background: #d1d5db;
        }
        .navbar .notification-link {
            position: relative;
        }
        .navbar .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            display: block;
            height: 8px;
            width: 8px;
            border-radius: 50%;
            background: #ef4444;
            border: 2px solid white;
        }

        /* Button Styles from kuanyik */
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Badge Styles from kuanyik */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-full {
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-registered {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-waitlisted {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-cancelled {
            background: #f3f4f6;
            color: #4b5563;
        }

        /* Table Styles from kuanyik */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Form Styles from kuanyik */
        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="file"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }

        /* Card Styles from kuanyik */
        .card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-item {
            padding: 15px;
            background: #f9fafb;
            border-radius: 6px;
            border-left: 4px solid #667eea;
        }

        .info-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #111827;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        form {
            display: inline;
        }

        .price {
            font-weight: 600;
            color: #059669;
        }

        .remaining {
            font-weight: 700;
            color: #667eea;
        }

        .remaining.low {
            color: #f59e0b;
        }

        .remaining.full {
            color: #ef4444;
        }
    </style>
    @stack('styles')
</head>
<body class="@yield('bodyClass', 'bg-gray-50')">
    <nav class="navbar">
        <a href="{{ route('homepage') }}" class="brand">Sportify</a>

        <div class="nav-right">
            <div class="nav-links">
                @auth
                    @php
                        $user = auth()->user();
                    @endphp
                    
                    {{-- Events - Students can see --}}
                    @if($user->isStudent() && Route::has('events.approved'))
                        <a href="{{ route('events.approved') }}">Events</a>
                    @endif
                    
                    {{-- Students: My Events --}}
                    @if($user->isStudent() && Route::has('payments.my-events'))
                        <a href="{{ route('payments.my-events') }}">My Events</a>
                    @endif

                    {{-- Committee: Dashboard + Transactions --}}
                    @if($user->isCommittee())
                        @if(Route::has('committee.dashboard'))
                            <a href="{{ route('committee.dashboard') }}">Dashboard</a>
                        @endif
                        @if(Route::has('payments.transaction-history'))
                            <a href="{{ route('payments.transaction-history') }}">Transactions</a>
                        @endif
                    @endif
                    
                    {{-- Admin: Events, Facilities, Users --}}
                    @if($user->isAdmin())
                        @if(Route::has('admin.events.index'))
                            <a href="{{ route('admin.events.index') }}">Events</a>
                        @endif
                        @if(Route::has('facilities.index'))
                            <a href="{{ route('facilities.index') }}">Facilities</a>
                        @endif
                        @if(Route::has('admin.users.index'))
                            <a href="{{ route('admin.users.index') }}">Users</a>
                        @endif
                    @endif
                    
                    {{-- Notifications - All authenticated users --}}
                    @if(Route::has('notifications.index'))
                        <a href="{{ route('notifications.index') }}" class="notification-link">
                            Notifications
                            @php
                                $unreadCount = $user->unreadNotifications->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="notification-badge"></span>
                            @endif
                        </a>
                    @endif
                @endauth
            </div>

            <div class="user-info">
                @auth
                    <span>Welcome, {{ auth()->user()->name }}</span>
                    @if(Route::has('profile.show'))
                        <a href="{{ route('profile.show') }}" class="logout-btn">Profile</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">Log Out</button>
                    </form>
                @else
                    @if(Route::has('login'))
                        <a href="{{ route('login') }}" class="logout-btn">Login</a>
                    @endif
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="logout-btn" style="background-color: #10b981; color: white;">Register</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <main class="@yield('mainClass', 'max-w-7xl mx-auto py-6 sm:px-6 lg:px-8')">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html>
