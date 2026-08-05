<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaksi;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $metode  = $request->input('metode');

        $query = Transaksi::with(['booking.layanan', 'booking.barber', 'booking.user'])->latest();

        if ($search) {
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('qr_code', 'LIKE', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'LIKE', "%{$search}%")
                  ->orWhere('no_hp', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'LIKE', "%{$search}%"))
                  ->orWhereHas('barber', fn($b) => $b->where('name', 'LIKE', "%{$search}%"))
                  ->orWhereHas('layanan', fn($l) => $l->where('nama_layanan', 'LIKE', "%{$search}%"));
            });
        }

        if ($metode && in_array($metode, ['tunai', 'EDC', 'transfer'])) {
            $query->where('metode_pembayaran', $metode);
        }

        $transaksi = $query->paginate(15)->withQueryString();

        $totalPendapatan   = Transaksi::where('status_pembayaran', 'lunas')->sum('total_harga');
        $totalTransaksi    = Transaksi::count();
        $transaksiHariIni  = Transaksi::whereDate('tanggal_bayar', today())->count();
        $pendapatanHariIni = Transaksi::whereDate('tanggal_bayar', today())->sum('total_harga');

        return view('admin.transaksi.index', compact(
            'transaksi', 'search', 'metode',
            'totalPendapatan', 'totalTransaksi', 'transaksiHariIni', 'pendapatanHariIni'
        ), ['title' => 'Daftar Transaksi']);
    }

    public function create(Booking $booking)
    {
        if ($booking->transaksi) {
            return redirect()->route('admin.transaksi.index')->with('info', 'Pembayaran untuk booking ini sudah tercatat sebelumnya.');
        }

        $booking->load('user', 'barber', 'layanan');
        return view('admin.transaksi.create', compact('booking'), ['title' => 'Catat Pembayaran']);
    }

    public function store(Request $request, Booking $booking)
    {
        if ($booking->transaksi) {
            return redirect()->route('admin.transaksi.index')->with('error', 'Pembayaran untuk booking ini sudah tercatat sebelumnya.');
        }

        $validated = $request->validate([
            'metode_pembayaran' => 'required|in:tunai,EDC,transfer,qris',
        ]);

        $transaksi = Transaksi::create([
            'booking_id' => $booking->id,
            'user_id'           => $booking->user_id, // null jika booking walk-in
            'total_harga' => $booking->layanan->harga,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'status_pembayaran' => 'lunas',
            'tanggal_bayar' => now(),
        ]);

        $booking->update(['status' => 'completed']);
        if ($booking->jadwal) {
            $booking->jadwal->update(['status' => 'tersedia']);
        }

        // Kirim Notifikasi WhatsApp Webhook
        WhatsAppService::sendTransactionReceipt($transaksi);

        return redirect()->route('admin.transaksi.index')->with('success', 'Pembayaran tercatat.');
    }

    public function invoice(Transaksi $transaksi)
    {
        $transaksi->load(['booking.layanan', 'booking.barber', 'booking.user']);
        return view('admin.transaksi.invoice', compact('transaksi'), ['title' => 'Invoice Pembayaran']);
    }
}
