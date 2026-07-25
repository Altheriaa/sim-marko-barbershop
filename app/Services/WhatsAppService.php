<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format nomor telepon ke standar internasional 628xxx.
     */
    public static function formatPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0')) {
            return '62' . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '62')) {
            return $cleaned;
        }

        return $cleaned;
    }

    /**
     * Kirim pesan WhatsApp ke nomor tujuan.
     */
    public static function sendMessage(string $targetPhone, string $message): bool
    {
        $enabled  = config('services.whatsapp.enabled', true);
        $provider = config('services.whatsapp.provider', 'fonnte');
        $token    = config('services.whatsapp.token', '');

        $formattedPhone = self::formatPhone($targetPhone);

        if (!$enabled || empty($formattedPhone)) {
            Log::info("WhatsApp service skip: enabled={$enabled}, phone={$formattedPhone}");
            return false;
        }

        if (empty($token)) {
            Log::warning("WhatsApp Webhook skipped: Token WA belum dikonfigurasi di WA_TOKEN.");
            return false;
        }

        try {
            if ($provider === 'wablas') {
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->post('https://jharkhand.wablas.com/api/send-message', [
                    'phone'   => $formattedPhone,
                    'message' => $message,
                ]);
            } else {
                // Default API Provider: Fonnte (https://api.fonnte.com/send)
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->post('https://api.fonnte.com/send', [
                    'target'  => $formattedPhone,
                    'message' => $message,
                ]);
            }

            if ($response->successful()) {
                Log::info("WhatsApp message sent successfully to {$formattedPhone}");
                return true;
            }

            Log::error("WhatsApp send error [{$response->status()}]: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("WhatsApp service exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim konfirmasi booking baru ke WhatsApp pelanggan.
     */
    public static function sendBookingConfirmation(Booking $booking): bool
    {
        $booking->loadMissing(['user', 'barber', 'layanan', 'jadwal']);

        $phone    = $booking->customer_phone;
        $namaUser = $booking->customer_name;

        if (!$phone) {
            return false;
        }
        $kodeBooking = $booking->qr_code;
        $namaBarber  = $booking->barber->name ?? '-';
        $namaLayanan = $booking->layanan->nama_layanan ?? '-';
        $tanggal     = $booking->jadwal?->tanggal?->format('d/m/Y') ?? '-';
        $jamMulai    = $booking->jadwal?->jam_mulai ?? '-';
        $jamSelesai  = $booking->jadwal?->jam_selesai ?? '-';

        // Hanya kirim link QR jika booking online (pelanggan punya akun)
        $isOnline = !is_null($booking->user_id);
        $qrLine   = $isOnline
            ? "Tunjukkan QR Code berikut saat Anda tiba di lokasi:\n" . route('pelanggan.booking.qr', $booking) . "\n\n"
            : "Tunjukkan kode booking *{$kodeBooking}* saat Anda tiba di lokasi.\n\n";

        $message = "*MARKO BARBERSHOP - BOOKING BERHASIL* \n\n"
            . "Halo *{$namaUser}*,\n"
            . "Booking Anda di Marko Barbershop berhasil dicatat!\n\n"
            . "*Kode Booking:* {$kodeBooking}\n"
            . "*Barber:* {$namaBarber}\n"
            . "*Layanan:* {$namaLayanan}\n"
            . "*Tanggal:* {$tanggal} ({$jamMulai} - {$jamSelesai})\n\n"
            . $qrLine
            . "Terima kasih atas kunjungan Anda! 🙌";

        return self::sendMessage($phone, $message);
    }

    /**
     * Kirim bukti pembayaran / receipt ke WhatsApp pelanggan.
     */
    public static function sendTransactionReceipt(Transaksi $transaksi): bool
    {
        $transaksi->loadMissing(['booking.user', 'booking.barber', 'booking.layanan']);

        $booking  = $transaksi->booking;
        $phone    = $booking?->customer_phone;
        $namaUser = $booking?->customer_name ?? 'Pelanggan';

        if (!$phone) {
            return false;
        }

        $resiNo     = str_pad($transaksi->id, 4, '0', STR_PAD_LEFT);
        $namaBarber = $transaksi->booking->barber->name ?? '-';
        $namaLayanan= $transaksi->booking->layanan->nama_layanan ?? '-';
        $totalHarga = number_format($transaksi->total_harga, 0, ',', '.');
        $metode     = strtoupper($transaksi->metode_pembayaran);

        // Link invoice hanya untuk pelanggan online (punya akun)
        $isOnline    = !is_null($booking?->user_id);
        $invoiceLine = $isOnline
            ? "Lihat / Cetak Struk:\n" . route('pelanggan.transaksi.invoice', $transaksi) . "\n\n"
            : '';

        $message = "*MARKO BARBERSHOP - BUKTI PEMBAYARAN* \n\n"
            . "Halo *{$namaUser}*,\n"
            . "Pembayaran transaksi Anda telah berhasil kami terima!\n\n"
            . "*No. Resi:* #{$resiNo}\n"
            . "*Barber:* {$namaBarber}\n"
            . "*Layanan:* {$namaLayanan}\n"
            . "*Total Bayar:* Rp {$totalHarga} ({$metode})\n\n"
            . $invoiceLine
            . "Terima kasih telah mempercayai Marko Barbershop! ✨";

        return self::sendMessage($phone, $message);
    }

    /**
     * Kirim notifikasi WA pemberitahuan booking baru ke Admin.
     */
    public static function sendAdminBookingAlert(Booking $booking): bool
    {
        $adminPhone = config('services.whatsapp.admin_phone', env('WA_ADMIN_PHONE', ''));

        if (empty($adminPhone)) {
            return false;
        }

        $booking->loadMissing(['user', 'barber', 'layanan', 'jadwal']);

        $namaUser    = $booking->customer_name;
        $phoneUser   = $booking->customer_phone ?? '-';
        $kodeBooking = $booking->qr_code;
        $namaBarber  = $booking->barber->name ?? '-';
        $namaLayanan = $booking->layanan->nama_layanan ?? '-';
        $tanggal     = $booking->jadwal?->tanggal?->format('d/m/Y') ?? '-';
        $jamMulai    = $booking->jadwal?->jam_mulai ?? '-';
        $jamSelesai  = $booking->jadwal?->jam_selesai ?? '-';

        $adminBookingUrl = route('admin.booking.index');

        $message = "*MARKO BARBERSHOP - BOOKING BARU MASUK!* \n\n"
            . "Halo Admin,\n"
            . "Pelanggan *{$namaUser}* ({$phoneUser}) telah membuat booking baru:\n\n"
            . "*Kode Booking:* {$kodeBooking}\n"
            . "*Barber:* {$namaBarber}\n"
            . "*Layanan:* {$namaLayanan}\n"
            . "*Tanggal:* {$tanggal} ({$jamMulai} - {$jamSelesai})\n\n"
            . "Kelola Reservasi di Dashboard:\n"
            . "{$adminBookingUrl}";

        return self::sendMessage($adminPhone, $message);
    }
}
