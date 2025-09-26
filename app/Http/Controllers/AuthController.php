<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Petugas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    // Halaman login
    public function showLogin()
    {
        return view('backend.pages.auth.login');
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->with('error', 'Email atau password salah');
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('/')->with('success', 'Anda telah logout.');
    }
}
