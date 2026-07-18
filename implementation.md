# Implementation Guide
## Sistem Informasi Manajemen Pelayanan Barbershop & Hair Care (QR Code)
**Stack:** Laravel 12 · PHP 8.3 · TailAdmin Laravel · Tailwind CSS v4 · Alpine.js · Vite · MySQL 8.0

---

## 1. Setup Proyek

```bash
# Clone TailAdmin Laravel starter
git clone https://github.com/TailAdmin/tailadmin-laravel.git marko-barbershop
cd marko-barbershop

composer install
npm install

cp .env.example .env
php artisan key:generate

# Library QR Code
composer require simplesoftwareio/simple-qrcode

# Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_DATABASE=marko_barbershop
# DB_USERNAME=root
# DB_PASSWORD=

php artisan migrate
npm run dev   # atau: php artisan serve (di terminal terpisah)
```

Jalankan lewat **Herd** (web server lokal) untuk kemudahan virtual host, atau `php artisan serve` untuk pengujian cepat.

## 2. Struktur Direktori Utama

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                     # registrasi & login
│   │   ├── Admin/
│   │   │   ├── BarberController.php
│   │   │   ├── LayananController.php
│   │   │   ├── JadwalBarberController.php
│   │   │   ├── BookingController.php
│   │   │   ├── QrCodeController.php  # scan / check-in / check-out
│   │   │   └── TransaksiController.php
│   │   ├── Owner/
│   │   │   └── LaporanController.php
│   │   └── Pelanggan/
│   │       └── BookingController.php
│   └── Middleware/
│       └── RoleMiddleware.php        # cek role: admin/owner/pelanggan
├── Models/
│   ├── User.php
│   ├── Barber.php
│   ├── JadwalBarber.php
│   ├── Layanan.php
│   ├── Booking.php
│   └── Transaksi.php
resources/views/
├── layouts/                          # layout TailAdmin (sidebar per role)
├── auth/
├── admin/
├── owner/
└── pelanggan/
routes/
└── web.php
```

## 3. Migration (mengikuti skema Bab III skripsi)

```php
// users (tambahan kolom role & phone di migration bawaan Laravel)
Schema::table('users', function (Blueprint $table) {
    $table->string('phone')->nullable();
    $table->enum('role', ['admin', 'owner', 'pelanggan'])->default('pelanggan');
});

// barbers
Schema::create('barbers', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('phone', 15)->nullable();
    $table->string('photo')->nullable();
    $table->boolean('status')->default(true); // aktif/nonaktif
    $table->timestamps();
});

// layanan
Schema::create('layanan', function (Blueprint $table) {
    $table->id();
    $table->string('nama_layanan', 100);
    $table->text('deskripsi')->nullable();
    $table->bigInteger('harga');
    $table->integer('durasi_menit');
    $table->timestamps();
});

// jadwal_barber
Schema::create('jadwal_barber', function (Blueprint $table) {
    $table->id();
    $table->foreignId('barber_id')->constrained('barbers')->cascadeOnDelete();
    $table->date('tanggal');
    $table->time('jam_mulai');
    $table->time('jam_selesai');
    $table->enum('status', ['tersedia', 'penuh'])->default('tersedia');
    $table->timestamps();
});

// booking
Schema::create('booking', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
    $table->foreignId('barber_id')->constrained('barbers');
    $table->foreignId('layanan_id')->constrained('layanan');
    $table->foreignId('jadwal_id')->constrained('jadwal_barber');
    $table->string('sumber', 50)->default('online'); // online/walk-in
    $table->string('qr_code')->unique();
    $table->enum('status', ['pending', 'checked-in', 'completed', 'cancelled'])->default('pending');
    $table->dateTime('waktu_checkin')->nullable();
    $table->dateTime('waktu_checkout')->nullable();
    $table->foreignId('dibuat_oleh')->nullable()->constrained('users');
    $table->timestamps();
});

// transaksi
Schema::create('transaksi', function (Blueprint $table) {
    $table->id();
    $table->foreignId('booking_id')->constrained('booking');
    $table->foreignId('user_id')->constrained('users');
    $table->bigInteger('total_harga');
    $table->string('metode_pembayaran', 50); // tunai/EDC/transfer
    $table->enum('status_pembayaran', ['lunas', 'belum'])->default('belum');
    $table->dateTime('tanggal_bayar')->nullable();
    $table->timestamps();
});
```

Jalankan: `php artisan migrate`

## 4. Model & Relasi (Eloquent)

```php
// app/Models/Booking.php
class Booking extends Model
{
    protected $table = 'booking';
    protected $fillable = [
        'user_id', 'barber_id', 'layanan_id', 'jadwal_id',
        'sumber', 'qr_code', 'status', 'waktu_checkin',
        'waktu_checkout', 'dibuat_oleh',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function barber() { return $this->belongsTo(Barber::class); }
    public function layanan() { return $this->belongsTo(Layanan::class); }
    public function jadwal() { return $this->belongsTo(JadwalBarber::class, 'jadwal_id'); }
    public function transaksi() { return $this->hasOne(Transaksi::class); }
    public function admin() { return $this->belongsTo(User::class, 'dibuat_oleh'); }
}

// app/Models/Transaksi.php
class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'booking_id', 'user_id', 'total_harga',
        'metode_pembayaran', 'status_pembayaran', 'tanggal_bayar',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
}
```

## 5. Autentikasi & Role Middleware

Gunakan Laravel Breeze/Fortify (Blade) sebagai dasar, sesuaikan view login/register dengan layout TailAdmin.

```php
// app/Http/Middleware/RoleMiddleware.php
public function handle($request, Closure $next, ...$roles)
{
    if (!in_array($request->user()->role, $roles)) {
        abort(403);
    }
    return $next($request);
}

// routes/web.php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::resource('barbers', BarberController::class);
    Route::resource('layanan', LayananController::class);
    Route::resource('jadwal', JadwalBarberController::class);
    Route::get('booking/scan', [QrCodeController::class, 'scanForm'])->name('booking.scan');
    Route::post('booking/checkin', [QrCodeController::class, 'checkIn'])->name('booking.checkin');
    Route::post('booking/{booking}/checkout', [QrCodeController::class, 'checkOut'])->name('booking.checkout');
    Route::resource('transaksi', TransaksiController::class)->only(['index', 'store']);
});

Route::middleware(['auth', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('laporan', [LaporanController::class, 'index'])->name('owner.laporan');
    Route::get('laporan/export', [LaporanController::class, 'export'])->name('owner.laporan.export');
});

Route::middleware(['auth', 'role:pelanggan'])->prefix('booking')->group(function () {
    Route::get('/', [BookingController::class, 'create'])->name('booking.create');
    Route::post('/', [BookingController::class, 'store'])->name('booking.store');
    Route::get('/{booking}/qr', [BookingController::class, 'showQr'])->name('booking.qr');
});
```

## 6. Modul Reservasi & Generate QR Code (Pelanggan)

```php
// app/Http/Controllers/Pelanggan/BookingController.php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

public function store(Request $request)
{
    $validated = $request->validate([
        'barber_id'  => 'required|exists:barbers,id',
        'layanan_id' => 'required|exists:layanan,id',
        'jadwal_id'  => 'required|exists:jadwal_barber,id',
    ]);

    $kode = 'BOOK-' . Str::upper(Str::random(8));

    $booking = Booking::create([
        ...$validated,
        'user_id' => auth()->id(),
        'sumber'  => 'online',
        'qr_code' => $kode,
        'status'  => 'pending',
    ]);

    // tandai jadwal penuh jika kapasitas per slot = 1
    $booking->jadwal->update(['status' => 'penuh']);

    return redirect()->route('booking.qr', $booking)->with('success', 'Booking berhasil, tunjukkan QR Code saat tiba.');
}

public function showQr(Booking $booking)
{
    $qrSvg = QrCode::size(220)->generate($booking->qr_code);
    return view('pelanggan.booking.qr', compact('booking', 'qrSvg'));
}
```

## 7. Modul Check-in via Scan QR Code (Admin)

Gunakan library JS scanner (mis. `html5-qrcode` via CDN) di halaman Blade Admin, kirim hasil scan ke endpoint `checkIn`.

```php
// app/Http/Controllers/Admin/QrCodeController.php
public function checkIn(Request $request)
{
    $request->validate(['qr_code' => 'required|string']);

    $booking = Booking::where('qr_code', $request->qr_code)
        ->where('status', 'pending')
        ->firstOrFail();

    $booking->update([
        'status'        => 'checked-in',
        'waktu_checkin' => now(),
    ]);

    return response()->json([
        'message' => 'Check-in berhasil',
        'booking' => $booking->load('user', 'barber', 'layanan'),
    ]);
}

public function checkOut(Request $request, Booking $booking)
{
    $booking->update([
        'status'         => 'completed',
        'waktu_checkout' => now(),
    ]);

    return redirect()->route('admin.transaksi.create', $booking)
        ->with('info', 'Lanjutkan ke pencatatan pembayaran.');
}
```

Contoh potongan Blade untuk scanner (disematkan di halaman Admin, styling menyesuaikan komponen card TailAdmin):

```html
<div id="qr-reader" style="width: 320px"></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
  const scanner = new Html5Qrcode("qr-reader");
  scanner.start({ facingMode: "environment" }, { fps: 10 }, (decodedText) => {
    fetch("{{ route('booking.checkin') }}", {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
      body: JSON.stringify({ qr_code: decodedText }),
    }).then(r => r.json()).then(data => alert(data.message));
  });
</script>
```

## 8. Modul Transaksi (Admin)

```php
// app/Http/Controllers/Admin/TransaksiController.php
public function store(Request $request, Booking $booking)
{
    $validated = $request->validate([
        'metode_pembayaran' => 'required|in:tunai,EDC,transfer',
    ]);

    Transaksi::create([
        'booking_id'         => $booking->id,
        'user_id'            => $booking->user_id ?? auth()->id(),
        'total_harga'        => $booking->layanan->harga,
        'metode_pembayaran'  => $validated['metode_pembayaran'],
        'status_pembayaran'  => 'lunas',
        'tanggal_bayar'      => now(),
    ]);

    return redirect()->route('admin.booking.index')->with('success', 'Pembayaran tercatat.');
}
```

## 9. Modul Laporan (Owner)

```php
// app/Http/Controllers/Owner/LaporanController.php
public function index(Request $request)
{
    $query = Transaksi::with('booking.layanan', 'booking.barber', 'booking.user')
        ->where('status_pembayaran', 'lunas');

    if ($request->filled('dari') && $request->filled('sampai')) {
        $query->whereBetween('tanggal_bayar', [$request->dari, $request->sampai]);
    }

    $transaksi = $query->latest()->paginate(20);
    $totalPendapatan = $query->sum('total_harga');

    return view('owner.laporan.index', compact('transaksi', 'totalPendapatan'));
}

public function export()
{
    // gunakan package maatwebsite/excel atau barryvdh/laravel-dompdf
    // return Excel::download(new LaporanTransaksiExport, 'laporan-transaksi.xlsx');
}
```

## 10. Integrasi TailAdmin (UI)

- Gunakan **layout blade bawaan TailAdmin** (`layouts.admin`, `layouts.owner`, `layouts.pelanggan` — buat 3 varian sidebar sesuai role) sebagai parent layout tiap view.
- Komponen tabel TailAdmin (`x-tailadmin::table`) dipakai untuk daftar booking, layanan, jadwal, dan laporan transaksi.
- Komponen card/statistik dipakai di dashboard Owner (total pendapatan, jumlah booking hari ini, tingkat check-in).
- Alpine.js dipakai untuk interaksi ringan: modal konfirmasi hapus, toggle status barber (aktif/nonaktif), live search tabel.

## 11. Validasi Anti-Bentrok Jadwal

```php
// Form Request: StoreBookingRequest
public function rules()
{
    return [
        'jadwal_id' => [
            'required',
            Rule::exists('jadwal_barber', 'id')->where('status', 'tersedia'),
        ],
    ];
}
```

Slot jadwal yang sudah dipakai (status `penuh`) otomatis tidak muncul di dropdown pilihan pelanggan — mencegah double booking sesuai tujuan penelitian.

## 12. Rencana Pengujian (Black Box Testing)

| Fitur | Skenario Uji | Hasil Diharapkan |
|---|---|---|
| Registrasi | Input email terdaftar | Sistem tolak, tampilkan pesan error |
| Login | Kredensial salah | Sistem tolak, tetap di halaman login |
| Booking | Pilih jadwal yang sudah penuh | Slot tidak muncul di pilihan |
| QR Code | Scan kode yang sudah checked-in | Sistem tolak (booking sudah diproses) |
| Transaksi | Input metode pembayaran kosong | Validasi gagal, muncul pesan wajib diisi |
| Laporan | Filter tanggal tanpa data | Tampilkan halaman kosong, bukan error |

## 13. Checklist Deployment

- [ ] `php artisan config:cache` & `route:cache` untuk produksi
- [ ] `npm run build` (bukan `dev`) sebelum deploy
- [ ] Set `APP_ENV=production`, `APP_DEBUG=false`
- [ ] Backup database sebelum migrasi produksi
- [ ] HTTPS aktif (terutama karena ada proses login & QR Code check-in)

---
*Dokumen ini melengkapi PRD dan merupakan panduan teknis implementasi berdasarkan skema database & use case yang telah dirancang, disesuaikan dengan stack aktual: Laravel 12, PHP 8.3, TailAdmin Laravel.*
