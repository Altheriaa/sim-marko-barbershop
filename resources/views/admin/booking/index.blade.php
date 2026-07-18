@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Daftar Booking'" />
@if(session('success'))<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400">{{ session('success') }}</div>@endif
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Semua Booking</h3>
        <div class="flex gap-2">
            <a href="{{ route('admin.booking.scan') }}" class="inline-flex items-center rounded-lg bg-green-500 px-4 py-2 text-sm font-medium text-white hover:bg-green-600 transition">📷 Scan QR</a>
            <a href="{{ route('admin.booking.create') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">+ Walk-in</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Kode QR</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Barber</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Layanan</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Sumber</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $loop->iteration + ($bookings->currentPage() - 1) * $bookings->perPage() }}</td>
                    <td class="px-5 py-4 text-sm font-mono text-gray-800 dark:text-white/90">{{ $booking->qr_code }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $booking->user->name ?? 'Walk-in' }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $booking->barber->name }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $booking->layanan->nama_layanan }}</td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $booking->sumber === 'online' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' }}">{{ ucfirst($booking->sumber) }}</span></td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                        @elseif($booking->status === 'checked-in') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @elseif($booking->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">{{ ucfirst($booking->status) }}</span></td>
                    <td class="px-5 py-4">
                        @if($booking->status === 'checked-in')
                        <form action="{{ route('admin.booking.checkout', $booking) }}" method="POST" class="inline">@csrf
                            <button type="submit" class="text-sm text-green-600 hover:text-green-700 font-medium">Check-out</button>
                        </form>
                        @elseif($booking->status === 'completed' && !$booking->transaksi)
                        <a href="{{ route('admin.transaksi.create', $booking) }}" class="text-sm text-brand-500 hover:text-brand-600 font-medium">Bayar</a>
                        @else
                        <span class="text-sm text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4">{{ $bookings->links() }}</div>
</div>
@endsection
