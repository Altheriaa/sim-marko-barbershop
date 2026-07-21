<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooking = Booking::where('user_id', auth()->id())->count();
        $bookingAktif = Booking::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'checked-in'])
            ->count();
        $recentBookings = Booking::with(['barber', 'layanan', 'jadwal'])
            ->where('user_id', auth()->id())
            ->latest()->take(5)->get();

        return view('pelanggan.dashboard', compact(
            'totalBooking', 'bookingAktif', 'recentBookings'
        ), ['title' => 'Dashboard']);
    }
}
