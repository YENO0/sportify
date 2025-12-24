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
    </style>
</head>
<body class="bg-gray-50">
    <nav class="navbar">
        <a href="{{ route('homepage') }}" class="brand">Sportify</a>
        <div class="user-info">
            @auth
                <span>Welcome, {{ auth()->user()->name }}</span>
                <a href="{{ route('profile.show') }}" class="logout-btn">Profile</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="logout-btn">Login</a>
                <a href="{{ route('register') }}" class="logout-btn" style="background-color: #10b981; color: white;">Register</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
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
