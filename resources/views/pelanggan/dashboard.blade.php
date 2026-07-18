@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Dashboard'" />
<div class="grid grid-cols-1 gap-4 md:gap-6 sm:grid-cols-2">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <span class="text-sm text-gray-500 dark:text-gray-400">Total Booking Saya</span>
        <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $totalBooking }}</h4>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <span class="text-sm text-gray-500 dark:text-gray-400">Booking Aktif</span>
        <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $bookingAktif }}</h4>
    </div>
</div>

<div class="mt-6 flex gap-3">
    <a href="{{ route('pelanggan.booking.create') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600 transition">+ Buat Booking Baru</a>
    <a href="{{ route('pelanggan.booking.riwayat') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-6 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Riwayat Booking</a>
</div>

@if($recentBookings->count())
<div class="mt-6 rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Booking Terbaru</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Barber</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Layanan</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
            </tr></thead>
            <tbody>
                @foreach($recentBookings as $booking)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $booking->barber->name }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $booking->layanan->nama_layanan }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $booking->jadwal->tanggal->format('d/m/Y') }} {{ $booking->jadwal->jam_mulai }}</td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                        @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                        @elseif($booking->status === 'checked-in') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                        @elseif($booking->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                        @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">{{ ucfirst($booking->status) }}</span></td>
                    <td class="px-5 py-4">
                        @if($booking->status === 'pending')
                        <a href="{{ route('pelanggan.booking.qr', $booking) }}" class="text-sm text-brand-500 hover:text-brand-600">Lihat QR</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
