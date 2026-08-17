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

        $qrLink = route('pelanggan.booking.qr', $booking);

        $message = "*MARKO BARBERSHOP - BOOKING BERHASIL* \n\n"
            . "Halo *{$namaUser}*,\n"
            . "Booking Anda di Marko Barbershop berhasil dicatat!\n\n"
            . "*Kode Booking:* {$kodeBooking}\n"
            . "*Barber:* {$namaBarber}\n"
            . "*Layanan:* {$namaLayanan}\n"
            . "*Tanggal:* {$tanggal} ({$jamMulai} - {$jamSelesai})\n\n"
            . "Link Barcode / QR Code:\n"
            . "{$qrLink}\n\n"
            . "*Catatan:* Mohon datang 15 menit sebelum waktu yang sudah dijadwalkan ({$jamMulai}) dan tunjukkan QR Code / kode booking ke kasir.\n\n"
            . "Terima kasih atas kunjungan Anda! ";

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

        $invoiceUrl = route('transaksi.invoice.public', $transaksi);

        $message = "*MARKO BARBERSHOP - BUKTI PEMBAYARAN* \n\n"
            . "Halo *{$namaUser}*,\n"
            . "Pembayaran transaksi Anda telah berhasil kami terima!\n\n"
            . "*No. Resi:* #{$resiNo}\n"
            . "*Barber:* {$namaBarber}\n"
            . "*Layanan:* {$namaLayanan}\n"
            . "*Total Bayar:* Rp {$totalHarga} ({$metode})\n\n"
            . "Lihat / Cetak Struk:\n"
            . "{$invoiceUrl}\n\n"
            . "Terima kasih telah mempercayai Marko Barbershop! ";

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

        $adminBookingUrl = route('kasir.booking.index');

        $message = "*MARKO BARBERSHOP - BOOKING BARU MASUK!* \n\n"
            . "Halo Kasir,\n"
            . "Pelanggan *{$namaUser}* ({$phoneUser}) telah membuat booking baru:\n\n"
            . "*Kode Booking:* {$kodeBooking}\n"
            . "*Barber:* {$namaBarber}\n"
            . "*Layanan:* {$namaLayanan}\n"
            . "*Tanggal:* {$tanggal} ({$jamMulai} - {$jamSelesai})\n\n"
            . "Kelola Reservasi di Dashboard:\n"
            . "{$adminBookingUrl}";

        return self::sendMessage($adminPhone, $message);
    }

    /**
     * Kirim notifikasi WA status booking/transaksi selesai ke Admin.
     */
    public static function sendAdminBookingCompleted(Transaksi $transaksi): bool
    {
        $adminPhone = config('services.whatsapp.admin_phone', env('WA_ADMIN_PHONE', ''));

        if (empty($adminPhone)) {
            return false;
        }

        $transaksi->loadMissing(['booking.user', 'booking.barber', 'booking.layanan', 'booking.jadwal']);

        $booking     = $transaksi->booking;
        $namaUser    = $booking?->customer_name ?? 'Pelanggan';
        $phoneUser   = $booking?->customer_phone ?? '-';
        $kodeBooking = $booking?->qr_code ?? '-';
        $resiNo      = str_pad($transaksi->id, 4, '0', STR_PAD_LEFT);
        $namaBarber  = $booking?->barber?->name ?? '-';
        $namaLayanan = $booking?->layanan?->nama_layanan ?? '-';
        $totalHarga  = number_format($transaksi->total_harga, 0, ',', '.');
        $metode      = strtoupper($transaksi->metode_pembayaran);
        $waktuBayar  = $transaksi->tanggal_bayar ? $transaksi->tanggal_bayar->format('d/m/Y H:i') : now()->format('d/m/Y H:i');

        $adminTrxUrl = route('kasir.transaksi.index');

        $message = "*MARKO BARBERSHOP - LAYANAN & PEMBAYARAN SELESAI* \n\n"
            . "Halo Kasir,\n"
            . "Booking dan pembayaran berikut telah selesai diproses:\n\n"
            . "*No. Resi:* #{$resiNo}\n"
            . "*Kode Booking:* {$kodeBooking}\n"
            . "*Pelanggan:* {$namaUser} ({$phoneUser})\n"
            . "*Barber:* {$namaBarber}\n"
            . "*Layanan:* {$namaLayanan}\n"
            . "*Total Bayar:* Rp {$totalHarga} ({$metode})\n"
            . "*Waktu Selesai:* {$waktuBayar}\n"
            . "*Status:* Selesai (Completed)\n\n"
            . "Kelola Transaksi di Dashboard:\n"
            . "{$adminTrxUrl}";

        return self::sendMessage($adminPhone, $message);
    }
}
