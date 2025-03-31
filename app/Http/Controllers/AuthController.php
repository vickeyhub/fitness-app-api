<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index(){
        if (Auth::check()) {
            return redirect('/admin/dashboard'); // Redirect if logged in
        }
        return view('auth.index');
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $credentials = $request->only('email', 'password');

        if(Auth::attempt($credentials, $request->filled('remember'))){
            return redirect()->intended('admin/dashboard');
        }
        return back()->withErrors(['email' => "Invalid credentials"]);
    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('web-login');
    }
}
