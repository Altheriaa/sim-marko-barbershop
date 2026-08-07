<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
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

        return view('kasir.transaksi.index', compact(
            'transaksi', 'search', 'metode',
            'totalPendapatan', 'totalTransaksi', 'transaksiHariIni', 'pendapatanHariIni'
        ), ['title' => 'Daftar Transaksi']);
    }

    public function invoice(Transaksi $transaksi)
    {
        $transaksi->load(['booking.layanan', 'booking.barber', 'booking.user']);
        return view('kasir.transaksi.invoice', compact('transaksi'), ['title' => 'Invoice Pembayaran']);
    }
}
