<?php

namespace App\Http\Controllers;

use App\Models\Barber;
use App\Models\Booking;
use App\Models\Layanan;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // 1. Search Bookings
        $bookings = Booking::with(['user', 'barber', 'layanan'])
            ->where('qr_code', 'LIKE', "%{$query}%")
            ->orWhereHas('user', fn($q) => $q->where('name', 'LIKE', "%{$query}%"))
            ->orWhereHas('barber', fn($q) => $q->where('name', 'LIKE', "%{$query}%"))
            ->take(5)
            ->get();

        foreach ($bookings as $b) {
            $results[] = [
                'type'     => 'Booking',
                'title'    => $b->qr_code . ' — ' . ($b->user->name ?? 'Walk-in'),
                'subtitle' => ($b->layanan->nama_layanan ?? '-') . ' • Barber: ' . ($b->barber->name ?? '-'),
                'url'      => route('kasir.booking.index', ['status' => $b->status]),
                'icon'     => 'fa-calendar-check',
            ];
        }

        // 2. Search Barber
        $barbers = Barber::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->take(4)
            ->get();

        foreach ($barbers as $barber) {
            $results[] = [
                'type'     => 'Barber',
                'title'    => $barber->name,
                'subtitle' => 'No. HP: ' . $barber->phone . ' (' . ($barber->status ? 'Aktif' : 'Non-aktif') . ')',
                'url'      => route('kasir.barbers.index'),
                'icon'     => 'fa-user-nurse',
            ];
        }

        // 3. Search Layanan
        $layananList = Layanan::where('nama_layanan', 'LIKE', "%{$query}%")
            ->orWhere('deskripsi', 'LIKE', "%{$query}%")
            ->take(4)
            ->get();

        foreach ($layananList as $layanan) {
            $results[] = [
                'type'     => 'Layanan',
                'title'    => $layanan->nama_layanan,
                'subtitle' => 'Rp ' . number_format($layanan->harga, 0, ',', '.') . ' • Durasi ' . $layanan->durasi_menit . ' menit',
                'url'      => route('kasir.layanan.index'),
                'icon'     => 'fa-scissors',
            ];
        }

        // 4. Search Transaksi
        $transaksiList = Transaksi::with(['booking.user', 'booking.layanan'])
            ->where('id', 'LIKE', "%{$query}%")
            ->orWhereHas('booking', fn($q) => $q->where('qr_code', 'LIKE', "%{$query}%"))
            ->take(4)
            ->get();

        foreach ($transaksiList as $trx) {
            $results[] = [
                'type'     => 'Transaksi',
                'title'    => 'Resi #' . str_pad($trx->id, 4, '0', STR_PAD_LEFT) . ' — Rp ' . number_format($trx->total_harga, 0, ',', '.'),
                'subtitle' => ($trx->booking->layanan->nama_layanan ?? '-') . ' • ' . ($trx->booking->user->name ?? 'Walk-in'),
                'url'      => route('kasir.transaksi.invoice', $trx->id),
                'icon'     => 'fa-receipt',
            ];
        }

        return response()->json(['results' => $results]);
    }
}
