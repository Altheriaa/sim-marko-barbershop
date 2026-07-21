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
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

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
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');

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
    Route::get('/dashboard', [PelangganDashboardController::class, 'index'])->name('dashboard');

    // Booking
    Route::get('booking/create', [PelangganBookingController::class, 'create'])->name('booking.create');
    Route::post('booking', [PelangganBookingController::class, 'store'])->name('booking.store');
    Route::get('booking/{booking}/qr', [PelangganBookingController::class, 'showQr'])->name('booking.qr');
    Route::get('booking/riwayat', [PelangganBookingController::class, 'riwayat'])->name('booking.riwayat');
});
