<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException; // For login validation errors

class LoginController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        // If admin is already logged in, redirect to dashboard
        if (Auth::guard('user')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin_login'); // <<< Path to your admin login view
    }

    /**
     * Handle an admin login attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember'); // Check if "remember me" is checked

        // Attempt to log in using the 'admin' guard
        if (Auth::guard('user')->attempt($credentials, $remember)) {
            $request->session()->regenerate(); // Regenerate session to prevent fixation
            return redirect()->intended(route('admin.dashboard')); // Redirect to intended URL or admin dashboard
        }

        // If authentication fails
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')], // Use Laravel's built-in translation for "auth.failed"
        ]);

        // Or a simpler way:
        // return back()->withErrors([
        //     'email' => 'The provided credentials do not match our admin records.',
        // ])->onlyInput('email');
    }

    /**
     * Log the admin out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('user')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login'); // Redirect to admin login page after logout
    }

    // Optional: Constructor to apply guest middleware to login form if needed
    // public function __construct()
    // {
    //     $this->middleware('guest:admin')->except('logout');
    // }
}