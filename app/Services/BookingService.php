<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\JadwalBarber;
use App\Models\Layanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingService
{
    // Jam operasional & istirahat
    const JAM_BUKA   = '10:00';
    const JAM_TUTUP  = '23:45';
    const ISTIRAHAT  = [
        ['mulai' => '13:00', 'selesai' => '14:00', 'label' => 'istirahat makan siang (13:00–14:00)'],
        ['mulai' => '18:00', 'selesai' => '19:30', 'label' => 'istirahat maghrib (18:00–19:30)'],
    ];

    /**
     * Validasi waktu booking: jam operasional, istirahat, dan jam lalu.
     *
     * @return string|null Pesan error, atau null jika valid.
     */
    public static function validateBookingTime(string $tanggal, string $jamMulaiStr, Carbon $jamMulai, Carbon $jamSelesai, int $durasiMenit): ?string
    {
        // 1. Cek jam tidak di masa lalu jika booking untuk hari ini
        if ($tanggal === now()->toDateString() && $jamMulaiStr < now()->format('H:i')) {
            return 'Jam mulai yang dipilih sudah lewat untuk hari ini.';
        }

        // 2. Cek dalam jam operasional
        $buka  = Carbon::createFromFormat('H:i', self::JAM_BUKA);
        $tutup = Carbon::createFromFormat('H:i', self::JAM_TUTUP);

        if ($jamMulai->lt($buka) || $jamSelesai->gt($tutup)) {
            return 'Jam operasional barbershop adalah ' . self::JAM_BUKA . '–' . self::JAM_TUTUP . '. Pastikan jam mulai dan selesai (durasi ' . $durasiMenit . ' menit) dalam rentang tersebut.';
        }

        // 3. Cek jam istirahat
        foreach (self::ISTIRAHAT as $istirahat) {
            $iMulai   = Carbon::createFromFormat('H:i', $istirahat['mulai']);
            $iSelesai = Carbon::createFromFormat('H:i', $istirahat['selesai']);

            if ($jamMulai->lt($iSelesai) && $jamSelesai->gt($iMulai)) {
                return 'Jam yang dipilih bertabrakan dengan ' . $istirahat['label'] . '. Silakan pilih jam lain.';
            }
        }

        return null;
    }

    /**
     * Cek apakah user (pelanggan online) masih punya booking aktif yang belum selesai dibayar.
     * Max 1 booking aktif per pelanggan.
     */
    public static function hasActiveBooking(int $userId): bool
    {
        return Booking::where('user_id', $userId)
            ->whereIn('status', ['pending', 'checked-in'])
            ->exists();
    }

    /**
     * Buat jadwal + booking dalam satu transaksi database dengan lock untuk mencegah race condition.
     *
     * @param array $data Keys: barber_id, layanan_id, tanggal, jam_mulai, user_id (nullable),
     *                     nama_pelanggan (nullable), no_hp (nullable), sumber, dibuat_oleh (nullable)
     * @return array ['success' => bool, 'booking' => Booking|null, 'error' => string|null]
     */
    public static function createBooking(array $data): array
    {
        $layanan    = Layanan::findOrFail($data['layanan_id']);
        $jamMulai   = Carbon::createFromFormat('H:i', $data['jam_mulai']);
        $jamSelesai = $jamMulai->copy()->addMinutes($layanan->durasi_menit);
        $jamMulaiStr   = $jamMulai->format('H:i');
        $jamSelesaiStr = $jamSelesai->format('H:i');

        // Validasi waktu
        $timeError = self::validateBookingTime(
            $data['tanggal'],
            $jamMulaiStr,
            $jamMulai,
            $jamSelesai,
            $layanan->durasi_menit
        );

        if ($timeError) {
            return ['success' => false, 'booking' => null, 'error' => $timeError];
        }

        // Cek max 1 active booking untuk pelanggan online
        if (!empty($data['user_id']) && self::hasActiveBooking($data['user_id'])) {
            return [
                'success' => false,
                'booking' => null,
                'error'   => 'Anda masih memiliki booking aktif yang belum selesai. Selesaikan atau batalkan booking sebelumnya terlebih dahulu.',
            ];
        }

        // Buat jadwal & booking dalam transaction dengan lock untuk mencegah race condition
        try {
            $booking = DB::transaction(function () use ($data, $layanan, $jamMulaiStr, $jamSelesaiStr) {
                // Cek konflik: Hanya dianggap bentrok jika ada jadwal_barber berstatus 'penuh'
                // atau memiliki relasi booking berstatus 'pending' / 'checked-in'
                $konflik = JadwalBarber::where('barber_id', $data['barber_id'])
                    ->where('tanggal', $data['tanggal'])
                    ->where('status', 'penuh')
                    ->whereHas('bookings', function ($bQuery) {
                        $bQuery->whereIn('status', ['pending', 'checked-in']);
                    })
                    ->lockForUpdate()
                    ->where(function ($q) use ($jamMulaiStr, $jamSelesaiStr) {
                        $q->where('jam_mulai', '<', $jamSelesaiStr)
                          ->where('jam_selesai', '>', $jamMulaiStr);
                    })
                    ->exists();

                if ($konflik) {
                    throw new \Exception('Barber yang dipilih sudah memiliki booking pada jam tersebut. Pilih jam atau barber lain.');
                }

                // Create jadwal
                $jadwal = JadwalBarber::create([
                    'barber_id'   => $data['barber_id'],
                    'tanggal'     => $data['tanggal'],
                    'jam_mulai'   => $jamMulaiStr,
                    'jam_selesai' => $jamSelesaiStr,
                    'status'      => 'penuh',
                ]);

                $kode = 'BOOK-' . Str::upper(Str::random(8));

                // Create booking
                return Booking::create([
                    'user_id'        => $data['user_id'] ?? null,
                    'nama_pelanggan' => $data['nama_pelanggan'] ?? null,
                    'no_hp'          => $data['no_hp'] ?? null,
                    'barber_id'      => $data['barber_id'],
                    'layanan_id'     => $data['layanan_id'],
                    'jadwal_id'      => $jadwal->id,
                    'sumber'         => $data['sumber'] ?? 'online',
                    'qr_code'        => $kode,
                    'status'         => 'pending',
                    'dibuat_oleh'    => $data['dibuat_oleh'] ?? null,
                ]);
            });

            return ['success' => true, 'booking' => $booking, 'error' => null];
        } catch (\Exception $e) {
            return ['success' => false, 'booking' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Batalkan booking. Hanya bisa dibatalkan jika status pending.
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public static function cancelBooking(Booking $booking): array
    {
        if ($booking->status !== 'pending') {
            return [
                'success' => false,
                'message' => 'Booking hanya bisa dibatalkan jika statusnya masih pending. Status saat ini: ' . $booking->status,
            ];
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);

            // Bebaskan slot jadwal barber menjadi 'tersedia'
            if ($booking->jadwal) {
                $booking->jadwal->update(['status' => 'tersedia']);
            }
        });

        return ['success' => true, 'message' => 'Booking berhasil dibatalkan.'];
    }

    /**
     * Otomatis membatalkan booking pending yang sudah melewati jam_mulai + 30 menit toleransi (No-Show).
     * Membebaskan slot jadwal barber menjadi 'tersedia'.
     *
     * @return int Jumlah booking yang dibatalkan
     */
    public static function autoCancelExpiredBookings(): int
    {
        $now = now();
        $cancelledCount = 0;

        // Ambil booking pending beserta relasi jadwalnya
        $pendingBookings = Booking::with('jadwal')
            ->where('status', 'pending')
            ->get();

        foreach ($pendingBookings as $booking) {
            if (!$booking->jadwal) {
                continue;
            }

            // Gabungkan tanggal dan jam_mulai dari jadwal
            $tanggalStr = $booking->jadwal->tanggal ? $booking->jadwal->tanggal->format('Y-m-d') : null;
            $jamMulaiStr = $booking->jadwal->jam_mulai;

            if (!$tanggalStr || !$jamMulaiStr) {
                continue;
            }

            $waktuBookingMulai = Carbon::parse("{$tanggalStr} {$jamMulaiStr}");
            $batasToleransi = $waktuBookingMulai->copy()->addMinutes(30);

            // Jika waktu sekarang sudah melewati jam_mulai + 30 menit
            if ($now->greaterThanOrEqualTo($batasToleransi)) {
                DB::transaction(function () use ($booking) {
                    $booking->update(['status' => 'cancelled']);
                    if ($booking->jadwal) {
                        $booking->jadwal->update(['status' => 'tersedia']);
                    }
                });
                $cancelledCount++;
            }
        }

        return $cancelledCount;
    }

    /**
     * Return jam operasional config untuk JSON response.
     */
    public static function getJamOperasional(): array
    {
        return [
            'buka'      => self::JAM_BUKA,
            'tutup'     => self::JAM_TUTUP,
            'istirahat' => self::ISTIRAHAT,
        ];
    }
}
