@extends('layouts.app')

@section('content')
    <div class="top-link">
        <a href="{{ route('admin.users.index') }}">← Back to User Management</a>
    </div>
    <h1>Create User</h1>
    <p class="subtitle">Create a new student or committee member account.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1.1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
            <select id="role" name="role" required style="width:100%;padding:0.7rem 0.85rem;border-radius:0.6rem;border:1px solid rgba(148,163,184,0.5);background:rgba(15,23,42,0.9);color:#e5e7eb;font-size:0.95rem;outline:none;transition:border-color 0.15s ease,box-shadow 0.15s ease,background 0.15s ease;">
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

        <button type="submit" class="btn-primary">
            Create User
        </button>
    </form>

    <p class="muted-link">
        <a href="{{ route('admin.users.index') }}">Cancel and return to user management</a>
    </p>
@endsection




