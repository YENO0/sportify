<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Strategies\LoginContext;
use App\Strategies\DatabaseLoginStrategy;

class AuthController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha'  => ['required', 'captcha'],
        ]);

        // All self-registered users are students
        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'], // hashed via model cast
            'role'     => User::ROLE_STUDENT,
        ]);

        Auth::login($user);

        return redirect()->route('homepage');
    }

    /**
     * Reload CAPTCHA.
     */
    public function reloadCaptcha()
    {
        return response()->json([
            'captcha' => captcha_img(),
        ]);
    }

    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login.
     */
    public function login(Request $request)
    {
        $strategy = new DatabaseLoginStrategy();
        $loginContext = new LoginContext($strategy);
        return $loginContext->login($request);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
