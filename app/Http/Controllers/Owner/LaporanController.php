<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with('booking.layanan', 'booking.barber', 'booking.user')
            ->where('status_pembayaran', 'lunas');

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_bayar', [$request->dari, $request->sampai]);
        }

        $transaksi = $query->latest()->paginate(20);
        $totalPendapatan = (clone $query)->sum('total_harga');

        return view('owner.laporan.index', compact('transaksi', 'totalPendapatan'), ['title' => 'Laporan Pendapatan']);
    }

    public function cetak(Request $request)
    {
        $startDate = $request->input('dari', now()->startOfMonth()->toDateString());
        $endDate   = $request->input('sampai', now()->toDateString());

        $transaksi = Transaksi::with(['booking.layanan', 'booking.barber', 'booking.user'])
            ->where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_bayar', '>=', $startDate)
            ->whereDate('tanggal_bayar', '<=', $endDate)
            ->oldest('tanggal_bayar')
            ->get();

        return view('laporan.cetak', compact('transaksi', 'startDate', 'endDate'), ['title' => 'Cetak Laporan Performa Bisnis']);
    }

    public function export(Request $request)
    {
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }
}
