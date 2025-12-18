@extends('layouts.app')

@section('content')
    <div class="top-link">
        <a href="{{ route('login') }}">Back to login</a>
    </div>

    <h1>Forgot password</h1>
    <p class="subtitle">Enter your email and we will send you a reset link.</p>

    @if (session('status'))
        <div class="alert alert-danger" style="border-color:rgba(52,211,153,0.5);background:rgba(16,185,129,0.08);color:#bbf7d0;">
            {{ session('status') }}
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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">
            Send reset link
        </button>
    </form>
@endsection






