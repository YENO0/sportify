@extends('layouts.app')

@section('content')
<style>
    /* Full-width layout overrides */
    body { display: block; }
    .main-footer {
        text-align: center; padding: 2rem; margin-top: 2rem;
        background: #f0f4f8; /* Light grey background */
        border-top: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        font-size: 0.9rem; color: #4b5563; /* Darker text */
    }
    .footer-links {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 1rem;
    }
    .footer-links a {
        color: #4b5563; /* Darker text */
        text-decoration: none;
        font-size: 0.9rem;
    }
    .footer-links a:hover {
        color: #1f2937; /* Even darker on hover */
        text-decoration: underline;
    }
    
    /* Profile Page Specific Styles */
    .profile-header {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.8) 0%, rgba(240, 244, 248, 0.8) 100%), url(/assets/backgrounds/08.png); /* Lighter gradient */
        background-size: cover; background-position: center;
        padding: 4rem 2rem;
        text-align: center;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
    }
    .profile-header h1 { font-size: 2.5rem; font-weight: 700; color: #1f2937; /* Darker text */ }
    .content-area { padding: 3rem 2rem; max-width: 900px; margin: 0 auto; }
    .profile-container {
        display: grid;
        grid-template-columns: 240px 1fr;
        gap: 3rem;
    }
    .profile-sidebar .profile-picture {
        width: 200px; height: 200px; border-radius: 50%;
        object-fit: cover; border: 4px solid #d1d5db; /* Lighter border */
        margin-bottom: 1rem;
    }
    .profile-sidebar .cancel-btn {
        display: block; text-align: center; text-decoration: none;
        background: #e5e7eb; /* Light background */ color: #374151; /* Darker text */
        padding: 0.7rem 1.25rem; font-size: 0.9rem; font-weight: 600;
        border-radius: 0.5rem; transition: background 0.2s ease;
    }
    .profile-sidebar .cancel-btn:hover { background: #d1d5db; /* Darker hover background */ }

    /* Form styling */
    .form-container {
        background: #ffffff; /* White background */ border: 1px solid rgba(0, 0, 0, 0.1); /* Lighter border */
        border-radius: 0.75rem; padding: 2rem;
    }
    .alert {
        padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem;
        border: 1px solid;
    }
    .alert-error {
        background: #fee2e2; border-color: #fca5a5; color: #dc2626;
    }
    .alert-warning {
        background: #fefce8; border-color: #fde047; color: #a16207;
    }
    .alert ul { margin: 0; padding-left: 1.2rem; }
    .field { margin-bottom: 1.5rem; }
    label {
        display: block; font-size: 0.8rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.06em;
        color: #374151; /* Darker label */ margin-bottom: 0.5rem;
    }
    input[type="text"], input[type="email"], input[type="password"], input[type="file"], input[type="date"] {
        width: 100%; padding: 0.7rem 0.85rem; border-radius: 0.6rem;
        border: 1px solid rgba(209, 213, 219, 1); /* Light border */ background: #f9fafb; /* Lighter input background */
        color: #1f2937; /* Darker input text */ font-size: 0.95rem; outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }
    input:focus, select:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.3);
        background: #ffffff; /* White on focus */
    }
    .gender-options {
        display: flex;
        gap: 1.5rem;
        margin-top: 0.5rem;
        flex-wrap: wrap; /* Added for responsiveness */
    }
    .radio-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        color: #374151; /* Darker text */
        cursor: pointer;
    }
    .radio-option input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        width: 1rem;
        height: 1rem;
        border: 2px solid #9ca3af; /* Light border */
        border-radius: 50%;
        background-color: #f9fafb; /* Lighter background */
        transition: background-color 0.2s, border-color 0.2s;
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
    }
    .radio-option input[type="radio"]:checked {
        background-color: #2563eb; /* Blue checked state */
        border-color: #2563eb;
    }
    .radio-option input[type="radio"]:checked::before {
        content: '';
        display: block;
        width: 0.5rem;
        height: 0.5rem;
        background-color: #ffffff; /* Inner white dot */
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
    .radio-option input[type="radio"]:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    .button-group { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;}
    .btn-primary {
        width: 100%; border: none; padding: 0.8rem 1rem; border-radius: 0.75rem;
        font-weight: 600; font-size: 1rem; color: #ffffff;
        background: linear-gradient(135deg,#2563eb,#1d4ed8); /* Blue primary button */
        cursor: pointer; transition: transform 0.1s ease;
    }
    .btn-primary:hover { transform: translateY(-2px); }
</style>

<header class="profile-header">
    <h1>Edit Profile</h1>
</header>

<main class="content-area">
    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="profile-container">
            <aside class="profile-sidebar">
                @if ($user->profile_picture)
                    <img src="{{ asset('profile_pictures/' . $user->profile_picture) }}" alt="Profile Picture" class="profile-picture">
                @else
                    <div style="width: 200px; height: 200px; border-radius: 50%; background-color: #e5e7eb; display: flex; align-items: center; justify-content: center; border: 4px solid #cbd5e1; margin-bottom: 1rem;">
                         <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" style="color: #6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                @endif
                <div class="field">
                    <label for="profile_picture">Update Picture</label>
                    <input id="profile_picture" type="file" name="profile_picture">
                </div>
                <a href="{{ route('profile.show') }}" class="cancel-btn" style="margin-top:1rem;">Cancel</a>
            </aside>

            <section class="profile-main">
                <div class="form-container">
                    <div class="field">
                        <label for="name">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required onchange="checkEmailChange()">
                         <div id="email-change-notice" style="display:none;margin-top:0.75rem;padding:0.6rem;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.3);border-radius:0.5rem;font-size:0.85rem;color:#2563eb;">
                            ⚠️ Changing your email requires your current password for security.
                        </div>
                    </div>

                    <div class="field" id="current-password-field" style="display:none;">
                        <label for="current_password">Current Password <span style="color:#ef4444;">*</span></label>
                        <input id="current_password" type="password" name="current_password">
                    </div>

                    <div class="field">
                        <label>Gender</label>
                        <div class="gender-options" style="display:flex;gap:1.5rem;margin-top:0.5rem;">
                            <label class="radio-option">
                                <input type="radio" name="gender" value="male" {{ old('gender', $user->gender) === 'male' ? 'checked' : '' }} required>
                                Male
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="gender" value="female" {{ old('gender', $user->gender) === 'female' ? 'checked' : '' }} required>
                                Female
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="gender" value="other" {{ old('gender', $user->gender) === 'other' ? 'checked' : '' }} required>
                                Other
                            </label>
                        </div>
                        @error('gender')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="birthday">Birthday</label>
                        <input id="birthday" type="date" name="birthday" value="{{ old('birthday', $user->birthday ? $user->birthday->format('Y-m-d') : '') }}" required>
                        @error('birthday')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="contact">Contact Number</label>
                        <input id="contact" type="text" name="contact" value="{{ old('contact', $user->contact) }}" required>
                        @error('contact')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">New Password (optional)</label>
                        <input id="password" type="password" name="password" autocomplete="new-password">
                    </div>

                    <div class="field">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
                    </div>
                    
                    <button type="submit" class="btn-primary" style="margin-top: 1rem;">
                        Update Profile
                    </button>
                </div>
            </section>
        </div>
    </form>
</main>

<footer class="main-footer">
    <div class="footer-links">
        <a href="{{ route('homepage') }}">Home</a>
        <a href="{{ route('about') }}">About Us</a>
    </div>
    &copy; {{ date('Y') }} Sportify. All Rights Reserved.
</footer>

<script>
    const originalEmail = '{{ $user->email }}';
    
    function checkEmailChange() {
        const emailInput = document.getElementById('email');
        const currentPasswordField = document.getElementById('current-password-field');
        const emailNotice = document.getElementById('email-change-notice');
        const currentPasswordInput = document.getElementById('current_password');

        if (emailInput.value !== originalEmail) {
            currentPasswordField.style.display = 'block';
            emailNotice.style.display = 'block';
            currentPasswordInput.required = true;
        } else {
            currentPasswordField.style.display = 'none';
            emailNotice.style.display = 'none';
            currentPasswordInput.required = false;
            currentPasswordInput.value = '';
        }
    }
    
    // Check on page load in case of validation errors showing an old value
    window.addEventListener('DOMContentLoaded', checkEmailChange);
</script>
@endsection