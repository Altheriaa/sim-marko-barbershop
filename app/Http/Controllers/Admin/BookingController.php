<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\JadwalBarber;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'barber', 'layanan', 'jadwal'])
            ->latest()
            ->paginate(10);

        return view('admin.booking.index', compact('bookings'), ['title' => 'Daftar Booking']);
    }

    public function create()
    {
        $barbers = Barber::where('status', true)->get();
        $layanan = Layanan::all();
        $jadwal = JadwalBarber::where('status', 'tersedia')
            ->where('tanggal', '>=', now()->toDateString())
            ->with('barber')
            ->get();

        return view('admin.booking.create', compact('barbers', 'layanan', 'jadwal'), ['title' => 'Booking Walk-in']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'layanan_id' => 'required|exists:layanan,id',
            'jadwal_id' => 'required|exists:jadwal_barber,id',
            'nama_pelanggan' => 'nullable|string|max:255',
        ]);

        $kode = 'BOOK-' . Str::upper(Str::random(8));

        $booking = Booking::create([
            'barber_id' => $validated['barber_id'],
            'layanan_id' => $validated['layanan_id'],
            'jadwal_id' => $validated['jadwal_id'],
            'sumber' => 'walk-in',
            'qr_code' => $kode,
            'status' => 'pending',
            'dibuat_oleh' => auth()->id(),
        ]);

        // Tandai jadwal penuh
        $booking->jadwal->update(['status' => 'penuh']);

        return redirect()->route('admin.booking.index')->with('success', 'Booking walk-in berhasil dibuat. Kode: ' . $kode);
    }
}
