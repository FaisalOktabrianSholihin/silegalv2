<?php

namespace App\Http\Controllers;

use App\Support\Aktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $r)
    {
        $cred = $r->validate(['email' => 'required|email', 'password' => 'required']);

        if (! Auth::attempt($cred + ['is_active' => true], $r->boolean('remember'))) {
            Aktivitas::catat('login_gagal', 'Auth', "Percobaan login gagal untuk {$r->email}");
            return back()->withErrors(['email' => 'Email atau password salah, atau akun tidak aktif.'])->onlyInput('email');
        }

        $r->session()->regenerate();
        $r->user()->update(['last_login_at' => now()]);
        Aktivitas::catat('login', 'Auth', 'Berhasil login ke SILEGAL');

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $r)
    {
        Aktivitas::catat('logout', 'Auth', 'Logout dari SILEGAL');
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
