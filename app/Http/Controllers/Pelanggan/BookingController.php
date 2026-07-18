<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\JadwalBarber;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
    public function create()
    {
        $barbers = Barber::where('status', true)->get();
        $layanan = Layanan::all();
        $jadwal = JadwalBarber::where('status', 'tersedia')
            ->where('tanggal', '>=', now()->toDateString())
            ->with('barber')
            ->orderBy('tanggal')
            ->orderBy('jam_mulai')
            ->get();

        return view('pelanggan.booking.create', compact('barbers', 'layanan', 'jadwal'), ['title' => 'Buat Booking']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'layanan_id' => 'required|exists:layanan,id',
            'jadwal_id' => [
                'required',
                'exists:jadwal_barber,id',
                function ($attribute, $value, $fail) {
                    $jadwal = JadwalBarber::find($value);
                    if ($jadwal && $jadwal->status !== 'tersedia') {
                        $fail('Jadwal yang dipilih sudah penuh.');
                    }
                },
            ],
        ]);

        $kode = 'BOOK-' . Str::upper(Str::random(8));

        $booking = Booking::create([
            ...$validated,
            'user_id' => auth()->id(),
            'sumber' => 'online',
            'qr_code' => $kode,
            'status' => 'pending',
        ]);

        // Tandai jadwal penuh (kapasitas per slot = 1)
        $booking->jadwal->update(['status' => 'penuh']);

        return redirect()->route('pelanggan.booking.qr', $booking)->with('success', 'Booking berhasil! Tunjukkan QR Code saat tiba.');
    }

    public function showQr(Booking $booking)
    {
        // Pastikan booking milik user yang login
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $qrSvg = QrCode::size(220)->generate($booking->qr_code);
        $booking->load('barber', 'layanan', 'jadwal');

        return view('pelanggan.booking.qr', compact('booking', 'qrSvg'), ['title' => 'QR Code Booking']);
    }

    public function riwayat()
    {
        $bookings = Booking::with(['barber', 'layanan', 'jadwal', 'transaksi'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pelanggan.booking.riwayat', compact('bookings'), ['title' => 'Riwayat Booking']);
    }
}
