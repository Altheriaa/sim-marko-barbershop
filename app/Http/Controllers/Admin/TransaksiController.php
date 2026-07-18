<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with(['booking.layanan', 'booking.barber', 'booking.user'])
            ->latest()
            ->paginate(10);

        return view('admin.transaksi.index', compact('transaksi'), ['title' => 'Daftar Transaksi']);
    }

    public function create(Booking $booking)
    {
        $booking->load('user', 'barber', 'layanan');
        return view('admin.transaksi.create', compact('booking'), ['title' => 'Catat Pembayaran']);
    }

    public function store(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'metode_pembayaran' => 'required|in:tunai,EDC,transfer',
        ]);

        Transaksi::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id ?? auth()->id(),
            'total_harga' => $booking->layanan->harga,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'status_pembayaran' => 'lunas',
            'tanggal_bayar' => now(),
        ]);

        return redirect()->route('admin.booking.index')->with('success', 'Pembayaran tercatat.');
    }
}
