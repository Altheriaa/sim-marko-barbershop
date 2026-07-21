<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPendapatan = Transaksi::where('status_pembayaran', 'lunas')->sum('total_harga');
        $pendapatanBulanIni = Transaksi::where('status_pembayaran', 'lunas')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('total_harga');
        $totalBooking = Booking::count();
        $bookingBulanIni = Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('owner.dashboard', compact(
            'totalPendapatan', 'pendapatanBulanIni', 'totalBooking', 'bookingBulanIni'
        ), ['title' => 'Dashboard Owner']);
    }
}
