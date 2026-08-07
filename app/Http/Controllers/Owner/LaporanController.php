<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Filter tanggal (dukung param start_date / dari & end_date / sampai)
        $startDate = $request->input('start_date', $request->input('dari', now()->startOfMonth()->toDateString()));
        $endDate   = $request->input('end_date', $request->input('sampai', now()->toDateString()));

        // Total pendapatan periode
        $totalPendapatan = Transaksi::where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_bayar', '>=', $startDate)
            ->whereDate('tanggal_bayar', '<=', $endDate)
            ->sum('total_harga');

        // Total transaksi periode
        $totalTransaksi = Transaksi::where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_bayar', '>=', $startDate)
            ->whereDate('tanggal_bayar', '<=', $endDate)
            ->count();

        // Layanan terlaris
        $layananTerlaris = Booking::select('layanan_id', DB::raw('count(*) as total'))
            ->with('layanan')
            ->whereHas('transaksi', fn($q) => $q->where('status_pembayaran', 'lunas')
                ->whereDate('tanggal_bayar', '>=', $startDate)
                ->whereDate('tanggal_bayar', '<=', $endDate))
            ->groupBy('layanan_id')
            ->orderByDesc('total')
            ->first();

        // Distribusi layanan
        $distribusiLayanan = Booking::select('layanan_id', DB::raw('count(*) as total'))
            ->with('layanan')
            ->whereHas('transaksi', fn($q) => $q->where('status_pembayaran', 'lunas')
                ->whereDate('tanggal_bayar', '>=', $startDate)
                ->whereDate('tanggal_bayar', '<=', $endDate))
            ->groupBy('layanan_id')
            ->orderByDesc('total')
            ->get();

        $totalBookingDistribusi = $distribusiLayanan->sum('total') ?: 1;

        // Pendapatan per barber
        $pendapatanBarber = Transaksi::select('booking.barber_id', DB::raw('sum(transaksi.total_harga) as total'))
            ->join('booking', 'booking.id', '=', 'transaksi.booking_id')
            ->with('booking.barber')
            ->where('transaksi.status_pembayaran', 'lunas')
            ->whereDate('transaksi.tanggal_bayar', '>=', $startDate)
            ->whereDate('transaksi.tanggal_bayar', '<=', $endDate)
            ->groupBy('booking.barber_id')
            ->orderByDesc('total')
            ->get();

        // Tren pendapatan harian
        $trendHarian = Transaksi::select(
                DB::raw('DATE(tanggal_bayar) as tanggal'),
                DB::raw('SUM(total_harga) as total')
            )
            ->where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_bayar', '>=', $startDate)
            ->whereDate('tanggal_bayar', '<=', $endDate)
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Rincian transaksi (paginated)
        $transaksi = Transaksi::with(['booking.layanan', 'booking.barber', 'booking.user'])
            ->where('status_pembayaran', 'lunas')
            ->whereDate('tanggal_bayar', '>=', $startDate)
            ->whereDate('tanggal_bayar', '<=', $endDate)
            ->latest('tanggal_bayar')
            ->paginate(15)
            ->withQueryString();

        return view('kasir.laporan.index', compact(
            'totalPendapatan', 'totalTransaksi', 'layananTerlaris',
            'distribusiLayanan', 'totalBookingDistribusi',
            'pendapatanBarber', 'trendHarian', 'transaksi',
            'startDate', 'endDate'
        ), ['title' => 'Laporan & Statistik']);
    }

    public function cetak(Request $request)
    {
        $startDate = $request->input('start_date', $request->input('dari', now()->startOfMonth()->toDateString()));
        $endDate   = $request->input('end_date', $request->input('sampai', now()->toDateString()));

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
        return $this->cetak($request);
    }
}
