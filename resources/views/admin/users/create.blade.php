@extends('layouts.app')

@section('mainClass', 'max-w-none mx-0 py-0 px-0')

@section('content')
<style>
    /* Full-width layout overrides and re-usable styles (copy-pasted for self-contained file) */
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

    /* Page-specific styles for form pages */
    .page-header {
        background: #ffffff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        padding: 2rem 2.5rem;
    }
    .page-header h1 { font-size: 1.8rem; font-weight: 700; color: #1f2937; margin: 0;}
    .page-header p { font-size: 1rem; color: #4b5563; margin-top: 0.25rem; }

    .content-area { padding: 2.5rem; max-width: 800px; margin: 0 auto; }
    .form-container {
        background: #ffffff; border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 0.75rem; padding: 2rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .field { margin-bottom: 1.5rem; }
    label {
        display: block; font-size: 0.8rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #4b5563; margin-bottom: 0.5rem;
    }
    input[type="text"], input[type="email"], input[type="password"], select {
        width: 100%; padding: 0.7rem 0.85rem; border-radius: 0.6rem;
        border: 1px solid rgba(0, 0, 0, 0.15); background: #f9fafb;
        color: #1f2937; font-size: 0.95rem; outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }
    input:focus, select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.3);
        background: #ffffff;
    }
    .button-group { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;}
    .btn {
        display: inline-flex; /* Changed to flex for centering */
        align-items: center; /* Center content vertically */
        justify-content: center; /* Center content horizontally */
        min-width: 110px; /* Adjusted for consistent size */
        box-sizing: border-box; /* Include padding and border in the width */
        text-decoration: none; color: #ffffff;
        padding: 0.7rem 1.5rem; border-radius: 0.5rem; font-weight: 600;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none; cursor: pointer;
    }
    .btn-primary { background: linear-gradient(135deg, #2563eb, #1d4ed8); } /* Blue primary button */
    .btn-secondary { background: #e5e7eb; color: #374151; } /* Light secondary button */
    .btn-primary:hover, .btn-secondary:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); }

    .alert {
        padding: 0.8rem 1rem; border-radius: 0.5rem; font-size: 0.9rem; margin-bottom: 1.5rem;
        border: 1px solid;
    }
    .alert-error { background: #fee2e2; border-color: #fca5a5; color: #dc2626; }
    .error-text { color: #dc2626; font-size: 0.78rem; margin-top: 0.25rem; }
</style>

<header class="page-header">
    <h1>Create User</h1>
    <p>Create a new student or committee member account.</p>
</header>

<main class="content-area">
    <div class="form-container">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="field">
                <label for="name">Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="role">Role</label>
                <select id="role" name="role" required>
                    <option value="">Select a role</option>
                    <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                    <option value="committee" {{ old('role') === 'committee' ? 'selected' : '' }}>Committee</option>
                </select>
                @error('role')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <div class="button-group">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    Create User
                </button>
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