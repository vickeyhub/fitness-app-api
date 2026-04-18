<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return Auth::user()->user_type === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('app.dashboard');
        }

        return view('auth.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            if (Auth::user()->user_type !== 'admin') {
                Auth::logout();

                return back()->withErrors([
                    'email' => __('Use the member sign-in for trainer, gym, and user accounts.'),
                ]);
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => __('Invalid credentials')]);
    }

    public function logout(Request $request)
    {
        $wasAdmin = Auth::check() && Auth::user()->user_type === 'admin';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($wasAdmin ? 'web-login' : 'auth.login');
    }
}
