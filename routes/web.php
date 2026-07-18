<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\BarberController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\JadwalBarberController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\Pelanggan\BookingController as PelangganBookingController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Dashboard Redirect (root)
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'owner' => redirect()->route('owner.dashboard'),
        default => redirect()->route('pelanggan.dashboard'),
    };
})->name('home');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $todayBookings = \App\Models\Booking::whereDate('created_at', today())->count();
        $checkedIn = \App\Models\Booking::where('status', 'checked-in')->count();
        $totalBarbers = \App\Models\Barber::where('status', true)->count();
        $totalLayanan = \App\Models\Layanan::count();
        $recentBookings = \App\Models\Booking::with(['user', 'barber', 'layanan'])
            ->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'todayBookings', 'checkedIn', 'totalBarbers', 'totalLayanan', 'recentBookings'
        ), ['title' => 'Dashboard Admin']);
    })->name('dashboard');

    // Barber CRUD
    Route::resource('barbers', BarberController::class);
    Route::patch('barbers/{barber}/toggle-status', [BarberController::class, 'toggleStatus'])->name('barbers.toggle-status');

    // Layanan CRUD
    Route::resource('layanan', LayananController::class);

    // Jadwal Barber CRUD
    Route::resource('jadwal', JadwalBarberController::class);

    // Booking Management
    Route::get('booking', [AdminBookingController::class, 'index'])->name('booking.index');
    Route::get('booking/create', [AdminBookingController::class, 'create'])->name('booking.create');
    Route::post('booking', [AdminBookingController::class, 'store'])->name('booking.store');

    // QR Code Scanner
    Route::get('booking/scan', [QrCodeController::class, 'scanForm'])->name('booking.scan');
    Route::post('booking/checkin', [QrCodeController::class, 'checkIn'])->name('booking.checkin');
    Route::post('booking/{booking}/checkout', [QrCodeController::class, 'checkOut'])->name('booking.checkout');

    // Transaksi
    Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{booking}/create', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('transaksi/{booking}', [TransaksiController::class, 'store'])->name('transaksi.store');
});

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $totalPendapatan = \App\Models\Transaksi::where('status_pembayaran', 'lunas')->sum('total_harga');
        $pendapatanBulanIni = \App\Models\Transaksi::where('status_pembayaran', 'lunas')
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('total_harga');
        $totalBooking = \App\Models\Booking::count();
        $bookingBulanIni = \App\Models\Booking::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('owner.dashboard', compact(
            'totalPendapatan', 'pendapatanBulanIni', 'totalBooking', 'bookingBulanIni'
        ), ['title' => 'Dashboard Owner']);
    })->name('dashboard');

    // Laporan
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
});

/*
|--------------------------------------------------------------------------
| Pelanggan Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $totalBooking = \App\Models\Booking::where('user_id', auth()->id())->count();
        $bookingAktif = \App\Models\Booking::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'checked-in'])
            ->count();
        $recentBookings = \App\Models\Booking::with(['barber', 'layanan', 'jadwal'])
            ->where('user_id', auth()->id())
            ->latest()->take(5)->get();

        return view('pelanggan.dashboard', compact(
            'totalBooking', 'bookingAktif', 'recentBookings'
        ), ['title' => 'Dashboard']);
    })->name('dashboard');

    // Booking
    Route::get('booking/create', [PelangganBookingController::class, 'create'])->name('booking.create');
    Route::post('booking', [PelangganBookingController::class, 'store'])->name('booking.store');
    Route::get('booking/{booking}/qr', [PelangganBookingController::class, 'showQr'])->name('booking.qr');
    Route::get('booking/riwayat', [PelangganBookingController::class, 'riwayat'])->name('booking.riwayat');
});
