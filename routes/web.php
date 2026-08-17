<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BarberController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\JadwalBarberController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\Owner\TransaksiController as OwnerTransaksiController;
use App\Http\Controllers\Pelanggan\BookingController as PelangganBookingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Pelanggan\DashboardController as PelangganDashboardController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:1,60');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/global-search', [\App\Http\Controllers\GlobalSearchController::class, 'search'])->name('global.search')->middleware('auth');
Route::get('/notifications/unread', [\App\Http\Controllers\NotificationController::class, 'unread'])->name('notifications.unread')->middleware('auth');

// Profile Routes for All Roles
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Public Invoice Route for WhatsApp receipts
Route::get('/invoice/{transaksi}', function (\App\Models\Transaksi $transaksi) {
    $transaksi->load(['booking.layanan', 'booking.barber', 'booking.user']);
    return view('kasir.transaksi.invoice', compact('transaksi'), ['title' => 'Invoice #' . $transaksi->id]);
})->name('transaksi.invoice.public');

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
        'kasir', 'admin' => redirect()->route('kasir.dashboard'),
        'owner' => redirect()->route('owner.dashboard'),
        default => redirect()->route('pelanggan.dashboard'),
    };
})->name('home');

/*
|--------------------------------------------------------------------------
| Kasir Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:kasir,admin'])->prefix('kasir')->name('kasir.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // User CRUD
    Route::resource('users', UserController::class);

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
    Route::get('booking/jadwal-json', [AdminBookingController::class, 'getJadwalJson'])->name('booking.jadwal-json');
    Route::post('booking', [AdminBookingController::class, 'store'])->name('booking.store');
    Route::post('booking/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('booking.cancel');

    // QR Code Scanner
    Route::get('booking/scan', [QrCodeController::class, 'scanForm'])->name('booking.scan');
    Route::post('booking/checkin', [QrCodeController::class, 'checkIn'])->name('booking.checkin');
    Route::post('booking/{booking}/checkout', [QrCodeController::class, 'checkOut'])->name('booking.checkout');

    // Transaksi
    Route::get('transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{booking}/create', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('transaksi/{booking}', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('transaksi/{transaksi}/invoice', [TransaksiController::class, 'invoice'])->name('transaksi.invoice');

    // Laporan
    Route::get('laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/cetak', [AdminLaporanController::class, 'cetak'])->name('laporan.cetak');
});

/*
|--------------------------------------------------------------------------
| Owner Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

    // Transaksi
    Route::get('transaksi', [OwnerTransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('transaksi/{transaksi}/invoice', [OwnerTransaksiController::class, 'invoice'])->name('transaksi.invoice');

    // Laporan
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan');
    Route::get('laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
});

/*
|--------------------------------------------------------------------------
| Pelanggan Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pelanggan'])->prefix('pelanggan')->name('pelanggan.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PelangganDashboardController::class, 'index'])->name('dashboard');

    // Booking & Transaksi
    Route::get('booking/create', [PelangganBookingController::class, 'create'])->name('booking.create');
    Route::get('booking/jadwal-json', [PelangganBookingController::class, 'getJadwalJson'])->name('booking.jadwal-json');
    Route::post('booking', [PelangganBookingController::class, 'store'])->name('booking.store');
    Route::post('booking/{booking}/cancel', [PelangganBookingController::class, 'cancel'])->name('booking.cancel');
    Route::get('booking/{booking}/qr', [PelangganBookingController::class, 'showQr'])->name('booking.qr');
    Route::get('booking/riwayat', [PelangganBookingController::class, 'riwayat'])->name('booking.riwayat');
    Route::get('transaksi/{transaksi}/invoice', [PelangganBookingController::class, 'invoice'])->name('transaksi.invoice');
});
