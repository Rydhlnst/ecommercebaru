<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('web')->check() && Auth::guard('web')->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $user = Auth::guard('web')->user();
            if (! $user->is_admin) {
                Auth::guard('web')->logout();

                return back()->withInput($request->only('email'))->with('error', 'Anda tidak memiliki akses admin.');
            }
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, '.$user->name.'!');
        }

        return back()->withInput($request->only('email'))->with('error', 'Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil logout.');
    }
}
