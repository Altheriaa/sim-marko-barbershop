@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Dashboard'" />

{{-- Metric Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Booking Saya</span>
                <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totalBooking }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                <i class="fa-solid fa-calendar-days text-lg"></i>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Booking Aktif</span>
                <h4 class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ $bookingAktif }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400">
                <i class="fa-solid fa-circle-check text-lg"></i>
            </div>
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="mb-6 flex flex-wrap gap-3">
    <a href="{{ route('pelanggan.booking.create') }}"
        class="inline-flex items-center gap-2 rounded-xl border border-brand-500 bg-brand-50/50 px-5 py-2.5 text-xs font-bold text-brand-700 shadow-xs hover:bg-brand-500 hover:text-white dark:border-brand-500/40 dark:bg-brand-500/10 dark:text-brand-400 dark:hover:bg-brand-500 dark:hover:text-white transition-all">
        <i class="fa-solid fa-plus text-xs"></i> Buat Booking Baru
    </a>
    <a href="{{ route('pelanggan.booking.riwayat') }}"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition shadow-sm">
        <i class="fa-solid fa-clock-rotate-left text-gray-400"></i> Riwayat Booking
    </a>
</div>

@if($recentBookings->count())
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-base font-bold text-gray-800 dark:text-white/90">
            <i class="fa-solid fa-list-ul mr-2 text-brand-500"></i>Booking Terbaru
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/50 text-gray-500 dark:text-gray-400 font-semibold uppercase tracking-wider">
                    <th class="py-3.5 px-5">Barber</th>
                    <th class="py-3.5 px-5">Layanan</th>
                    <th class="py-3.5 px-5">Jadwal</th>
                    <th class="py-3.5 px-5">Status</th>
                    <th class="py-3.5 px-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @foreach($recentBookings as $booking)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                        <td class="py-4 px-5 font-semibold text-gray-900 dark:text-white">{{ $booking->barber->name }}</td>
                        <td class="py-4 px-5 text-gray-600 dark:text-gray-400">{{ $booking->layanan->nama_layanan }}</td>
                        <td class="py-4 px-5 text-gray-500 dark:text-gray-400">
                            @if($booking->jadwal)
                                <span class="font-medium">{{ $booking->jadwal->tanggal->format('d/m/Y') }}</span>
                                <span class="ml-1 text-gray-400">({{ $booking->jadwal->jam_mulai }})</span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="py-4 px-5">
                            @php
                                $statusClasses = [
                                    'pending'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'checked-in' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    'completed'  => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'cancelled'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                ];
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            @if($booking->status === 'pending')
                                <a href="{{ route('pelanggan.booking.qr', $booking) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-100 dark:bg-brand-950/40 dark:text-brand-400 transition">
                                    <i class="fa-solid fa-qrcode"></i> Lihat QR
                                </a>
                            @elseif($booking->transaksi)
                                <a href="{{ route('pelanggan.transaksi.invoice', $booking->transaksi) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-brand-500 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 transition">
                                    <i class="fa-solid fa-receipt text-brand-500"></i> Cetak Struk
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
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
