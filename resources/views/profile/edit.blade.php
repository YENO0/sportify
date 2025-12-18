@extends('layouts.app')

@section('content')
    <div class="top-link">
        <a href="{{ route('profile.show') }}">← Back to Profile</a>
    </div>
    <h1>Edit Profile</h1>
    <p class="subtitle">Update your account information.</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 1.1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="profile_picture">Profile Picture</label>
            <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                @if ($user->profile_picture)
                    <img src="{{ asset('profile_pictures/' . $user->profile_picture) }}" alt="Profile Picture" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-right: 1.5rem;">
                @else
                    <div style="width: 80px; height: 80px; border-radius: 50%; background-color: #334155; display: flex; align-items: center; justify-content: center; margin-right: 1.5rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
                <input id="profile_picture" type="file" name="profile_picture">
            </div>
            @error('profile_picture')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="name">Name</label>
            <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus>
            @error('name')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required onchange="checkEmailChange()">
            <div id="email-change-notice" style="display:none;margin-top:0.5rem;padding:0.5rem;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);border-radius:0.5rem;font-size:0.8rem;color:#bae6fd;">
                ⚠️ Changing your email requires your current password for security.
            </div>
            @error('email')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="field" id="current-password-field" style="display:none;">
            <label for="current_password">Current Password <span style="color:#f97373;">*</span></label>
            <input id="current_password" type="password" name="current_password">
            @error('current_password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password">New Password (leave blank to keep current password)</label>
            <input id="password" type="password" name="password">
            @error('password')
                <div class="error-text">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation">
        </div>

        <button type="submit" class="btn-primary">
            Update Profile
        </button>
    </form>

    <p class="muted-link">
        <a href="{{ route('profile.show') }}">Cancel and return to profile</a>
    </p>

    <script>
        const originalEmail = '{{ $user->email }}';
        
        function checkEmailChange() {
            const emailInput = document.getElementById('email');
            const currentPasswordField = document.getElementById('current-password-field');
            const emailNotice = document.getElementById('email-change-notice');
            
            if (emailInput.value !== originalEmail) {
                currentPasswordField.style.display = 'block';
                emailNotice.style.display = 'block';
                document.getElementById('current_password').required = true;
            } else {
                currentPasswordField.style.display = 'none';
                emailNotice.style.display = 'none';
                document.getElementById('current_password').required = false;
                document.getElementById('current_password').value = '';
            }
        }
        
        // Check on page load in case of validation errors
        window.addEventListener('DOMContentLoaded', checkEmailChange);
    </script>
@endsection

