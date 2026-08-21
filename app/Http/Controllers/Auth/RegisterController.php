<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\RateLimiter;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $deviceId = $request->cookie('device_id') ?? (string) \Illuminate\Support\Str::uuid();

        return response()
            ->view('pages.auth.signup', ['title' => 'Daftar Akun'])
            ->cookie('device_id', $deviceId, 60 * 24 * 365); // 1 tahun
    }

    public function register(Request $request)
    {
        $deviceId = $request->cookie('device_id') ?? md5($request->userAgent() . '|' . $request->ip());
        $key = 'register-device:' . $deviceId;

        // Maksimal 1 kali percobaan / registrasi per perangkat dalam 1 jam
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);

            return back()->withInput($request->except('password', 'password_confirmation'))->withErrors([
                'email' => "Terlalu banyak percobaan registrasi dari perangkat Anda. Silakan coba lagi dalam {$minutes} menit.",
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        RateLimiter::hit($key, 3600); // 1 jam decay

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'pelanggan',
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('pelanggan.dashboard')->with('success', 'Registrasi berhasil!');
    }
}
