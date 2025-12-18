@extends('layouts.app')

@section('content')
    <div class="top-link">
        <a href="{{ route('login') }}">Already registered?</a>
    </div>
    <h1>Create account</h1>
    <p class="subtitle">Join Sportify to manage your sports data and access the dashboard.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1.1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/register') }}">
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
            <label for="captcha">Captcha</label>
            <div class="captcha-container">
                <span class="captcha-image">{!! captcha_img() !!}</span>
                <button type="button" class="btn-secondary reload" id="reload">&#x21bb;</button>
            </div>
            <input id="captcha" type="text" name="captcha" required>
            @error('captcha')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn-primary">
            Sign up
        </button>
    </form>

    <p class="muted-link">
        Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
    </p>
@endsection

@push('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript">
    $('#reload').click(function () {
        $.ajax({
            type: 'GET',
            url: '{{ route('captcha.reload') }}',
            success: function (data) {
                $(".captcha-image").html(data.captcha);
            }
        });
    });
</script>
@endpush






