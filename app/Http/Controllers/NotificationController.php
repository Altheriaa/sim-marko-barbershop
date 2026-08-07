<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function unread(Request $request)
    {
        $user = auth()->user();
        $role = $user?->role ?? 'kasir';

        $query = Booking::with(['user', 'layanan', 'barber']);

        if ($role === 'pelanggan') {
            $query->where('user_id', $user->id);
        }

        $latestBookings = $query->latest()->take(6)->get();

        $unreadCount = Booking::where('status', 'pending')
            ->when($role === 'pelanggan', fn($q) => $q->where('user_id', $user->id))
            ->count();

        $items = $latestBookings->map(function ($b) use ($role) {
            $targetUrl = match ($role) {
                'owner'     => route('owner.transaksi.index'),
                'pelanggan' => route('pelanggan.booking.riwayat'),
                default     => route('kasir.booking.index'),
            };

            return [
                'id'               => $b->id,
                'user_name'        => $b->customer_name,
                'initial'          => strtoupper(substr($b->customer_name, 0, 1)),
                'layanan'          => $b->layanan->nama_layanan ?? '-',
                'barber'           => $b->barber->name ?? '-',
                'status'           => $b->status,
                'created_at_human' => $b->created_at?->diffForHumans() ?? 'Baru saja',
                'target_url'       => $targetUrl,
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'latest_id'    => $latestBookings->first()?->id ?? 0,
            'items'        => $items,
        ]);
    }
}
