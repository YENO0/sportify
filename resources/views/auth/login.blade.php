@extends('layouts.app')

@section('content')

<style>

    .checkbox-label {

        margin: 0;

        text-transform: none;

        letter-spacing: 0;

        font-weight: 500;

        font-size: 0.85rem;

        color: #4b5563; /* Darker text for light theme */

    }

</style>

    <h1>Welcome back</h1>

    <p class="subtitle">Sign in to access your Sportify dashboard.</p>



    @if ($errors->any())

        <div class="alert alert-danger">

            <ul style="margin: 0; padding-left: 1.1rem;">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

                @if ($errors->has('throttle'))

                    <li>{{ $errors->first('throttle') }}</li>

                @endif

            </ul>

        </div>

    @endif



    <form method="POST" action="{{ url('/login') }}">

        @csrf



        <div class="field">

            <label for="email">Email</label>

            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

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



        <div class="field" style="display:flex;align-items:center;gap:0.4rem;margin-top:0.1rem;">

            <input id="remember" type="checkbox" name="remember" style="width:auto;">

            <label for="remember" class="checkbox-label">

                Remember me

            </label>

        </div>



        <button type="submit" class="btn-primary">

            Sign in

        </button>

    </form>



    <p class="muted-link">

        <a href="{{ route('password.request') }}">Forgot your password?</a>

    </p>



    <p class="muted-link">

        Need an account?

        <a href="{{ route('register') }}">Sign up</a>

    </p>
@endsection


