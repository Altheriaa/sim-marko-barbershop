<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function scanForm()
    {
        return view('kasir.scan-qr', ['title' => 'Scan QR Code']);
    }

    public function checkIn(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);

        $booking = Booking::where('qr_code', $request->qr_code)
            ->where('status', 'pending')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau booking sudah diproses.',
            ], 404);
        }

        $booking->update([
            'status' => 'checked-in',
            'waktu_checkin' => now(),
        ]);

        $pelangganNama = $booking->user ? $booking->user->name : 'Walk-in';
        session()->flash('success', "Check-in berhasil! Status booking {$booking->kode_booking} ({$pelangganNama}) diubah menjadi Checked-in.");

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil!',
            'redirect' => route('kasir.booking.index'),
            'booking' => $booking->load('user', 'barber', 'layanan'),
        ]);
    }

    public function checkOut(Booking $booking)
    {
        $booking->update([
            'status' => 'completed',
            'waktu_checkout' => now(),
        ]);

        if ($booking->jadwal) {
            $booking->jadwal->update(['status' => 'tersedia']);
        }

        return redirect()->route('kasir.transaksi.create', $booking)
            ->with('info', 'Lanjutkan ke pencatatan pembayaran.');
    }
}
