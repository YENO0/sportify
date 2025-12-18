@extends('layouts.app')

@section('content')
    <div class="top-link">
        <a href="{{ route('homepage') }}">← Back to Homepage</a>
    </div>
    <h1>Create Committee Member</h1>
    <p class="subtitle">Add a new committee member account. Committee members can manage events and students.</p>

    @if (session('success'))
        <div class="alert" style="background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.4); color: #bbf7d0; margin-bottom: 0.9rem;">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1.1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.committee.store') }}">
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
            Create Committee Member
        </button>
    </form>

    <p class="muted-link">
        <a href="{{ route('homepage') }}">Return to homepage</a>
    </p>
@endsection




