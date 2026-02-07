<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string', // Change from 'email' to 'string' to allow Phone No
            'password' => 'required',
        ]);

        $loginType = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'nohp';

        $credentials = [
            $loginType => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            if ($user->isPeternak()) {
                return redirect('/dashboard-peternak');
            } elseif ($user->isPengelola() || $user->isAdmin()) {
                return redirect('/dashboard-pengelola');
            }

            // Login berhasil secara credential, tapi role tidak valid
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Akun anda tidak memiliki akses yang valid.',
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'Email/No HP atau password tidak valid.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
