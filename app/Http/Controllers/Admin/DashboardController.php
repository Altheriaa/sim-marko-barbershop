<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Barber;
use App\Models\Layanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todayBookings = Booking::whereDate('created_at', today())->count();
        $checkedIn = Booking::where('status', 'checked-in')->count();
        $totalBarbers = Barber::count();
        $barbers = Barber::all();
        $totalLayanan = Layanan::count();
        $recentBookings = Booking::with(['user', 'barber', 'layanan'])
            ->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'todayBookings', 'checkedIn', 'totalBarbers', 'barbers', 'totalLayanan', 'recentBookings'
        ), ['title' => 'Dashboard Admin']);
    }
}
