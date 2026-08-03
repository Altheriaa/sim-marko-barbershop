<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        // Calculate role-specific stats
        $stats = [
            'total_booking'    => 0,
            'booking_selesai'  => 0,
            'total_transaksi'  => 0,
            'total_pendapatan' => 0,
        ];

        if ($user->role === 'pelanggan') {
            $stats['total_booking']   = Booking::where('user_id', $user->id)->count();
            $stats['booking_selesai'] = Booking::where('user_id', $user->id)->where('status', 'completed')->count();
        } elseif ($user->role === 'admin') {
            $stats['total_booking']   = Booking::count();
            $stats['total_transaksi'] = Transaksi::count();
        } elseif ($user->role === 'owner') {
            $stats['total_transaksi']  = Transaksi::count();
            $stats['total_pendapatan'] = Transaksi::sum('total_bayar');
        }

        return view('pages.profile', compact('user', 'stats'), ['title' => 'Profil Saya']);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.current_password' => 'Password saat ini salah.',
            'password.min'                     => 'Password baru minimal 8 karakter.',
            'password.confirmed'               => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success_password', 'Password berhasil diperbarui!');
    }
}
