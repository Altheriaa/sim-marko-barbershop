<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function scanForm()
    {
        return view('admin.scan-qr', ['title' => 'Scan QR Code']);
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

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil!',
            'booking' => $booking->load('user', 'barber', 'layanan'),
        ]);
    }

    public function checkOut(Booking $booking)
    {
        $booking->update([
            'status' => 'completed',
            'waktu_checkout' => now(),
        ]);

        return redirect()->route('admin.transaksi.create', $booking)
            ->with('info', 'Lanjutkan ke pencatatan pembayaran.');
    }
}
