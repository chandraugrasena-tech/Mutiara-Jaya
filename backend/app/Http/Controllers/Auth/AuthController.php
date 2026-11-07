<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Login, register, dan logout untuk halaman web (session)
class AuthController extends Controller
{
    public function showLogin()
    {
        // pesan muncul kalau pengunjung mencoba memakai keranjang atau promo sebelum login
        $notice = (request()->boolean('need') || session()->has('url.intended'))
            ? 'Silakan masuk dulu untuk memasukkan menu ke keranjang, memakai promo, dan memesan.'
            : null;

        return view('auth.login', ['title' => 'Masuk', 'notice' => $notice]);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'required' => 'Kolom :attribute harus diisi',
            'email' => 'Format email tidak valid',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau password salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        // admin langsung ke dashboard, pelanggan kembali ke halaman sebelumnya
        $default = Auth::user()->isAdmin() ? route('admin.dashboard') : route('home');

        return redirect()->intended($default);
    }

    public function showRegister()
    {
        $notice = (request()->boolean('need') || session()->has('url.intended'))
            ? 'Daftar atau masuk dulu untuk memasukkan menu ke keranjang, memakai promo, dan memesan.'
            : null;

        return view('auth.register', ['title' => 'Daftar', 'notice' => $notice]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'required' => 'Kolom :attribute harus diisi',
            'email.unique' => 'Email sudah terdaftar',
            'min' => 'Kolom :attribute minimal :min karakter',
            'confirmed' => 'Konfirmasi password tidak sama',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],   // di-hash otomatis (cast 'hashed')
            'role' => 'customer',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'Selamat datang di Mutiara Jaya, '.$user->name.'!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
