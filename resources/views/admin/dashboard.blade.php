@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Dashboard Admin'" />

    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 gap-4 md:gap-6 sm:grid-cols-2 xl:grid-cols-4">
        <!-- Booking Hari Ini -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Booking Hari Ini</span>
                    <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $todayBookings }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10">
                    <svg class="text-brand-500" width="24" height="24" fill="none" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                            d="M8 2C8.41421 2 8.75 2.33579 8.75 2.75V3.75H15.25V2.75C15.25 2.33579 15.5858 2 16 2C16.4142 2 16.75 2.33579 16.75 2.75V3.75H18.5C19.7426 3.75 20.75 4.75736 20.75 6V19C20.75 20.2426 19.7426 21.25 18.5 21.25H5.5C4.25736 21.25 3.25 20.2426 3.25 19V6C3.25 4.75736 4.25736 3.75 5.5 3.75H7.25V2.75C7.25 2.33579 7.58579 2 8 2Z"
                            fill="currentColor" />
                    </svg>
                </div>
            </div>
        </div>
        <!-- Sedang Dilayani -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Sedang Dilayani</span>
                    <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $checkedIn }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 dark:bg-green-500/10">
                    <svg class="text-green-500" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z" />
                    </svg>
                </div>
            </div>
        </div>
        <!-- Total Barber Aktif -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Barber Aktif</span>
                    <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $totalBarbers }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-500/10">
                    <svg class="text-purple-500" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                    </svg>
                </div>
            </div>
        </div>
        <!-- Total Layanan -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Total Layanan</span>
                    <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $totalLayanan }}</h4>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/10">
                    <svg class="text-orange-500" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout Grid -->
    <div class="mt-6 grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Column (Span 2) -->
        <div class="xl:col-span-2 space-y-6">

            <!-- Jadwal Hari Ini (Layout Placeholder) -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Jadwal Hari Ini</h3>
                    <a href="#" class="text-sm text-brand-500 hover:text-brand-600">Lihat Semua</a>
                </div>
                <div class="p-5">
                    <div class="flex flex-col items-center justify-center py-8 text-gray-500 dark:text-gray-400">
                        <!-- Placeholder for list layout -->
                        <svg class="w-12 h-12 mb-3 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-sm">List jadwal akan ditampilkan di sini</span>
                    </div>
                </div>
            </div>

            <!-- Booking Terbaru (Original Data) -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Booking Terbaru</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">
                                    Pelanggan</th>
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Barber
                                </th>
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Layanan
                                </th>
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status
                                </th>
                                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentBookings as $booking)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">
                                        {{ $booking->user->name ?? 'Walk-in' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $booking->barber->name }}
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $booking->layanan->nama_layanan }}</td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold
                                                @if($booking->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                                                @elseif($booking->status === 'checked-in') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                                                @elseif($booking->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                                                @endif">
                                            {{ ucfirst($booking->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $booking->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum
                                        ada booking.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column (Span 1) -->
        <div class="space-y-6">

            <!-- Check-in Placeholder -->
            <div
                class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-6 text-center flex flex-col items-center">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white/90 mb-2">Check-in</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Scan QR Code pelanggan di sini</p>

                <div
                    class="aspect-square w-full max-w-[200px] bg-gray-50 dark:bg-gray-800/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700 flex items-center justify-center mb-6">
                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                    </svg>
                </div>

                <a href="{{ route('admin.booking.scan') }}"
                    class="w-full inline-flex justify-center items-center py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-brand-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Buka Pemindai QR
                </a>
            </div>

            <!-- Barber Status Placeholder -->
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Barber Status</h3>
                </div>
                <div class="p-5 space-y-5">
                    @forelse ($barbers as $barber)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-brand-500 overflow-hidden border border-gray-200 dark:border-gray-700">
                                    @if($barber->photo)
                                        <img src="{{ Storage::url($barber->photo) }}" class="w-full h-full object-cover">
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    @endif
                                </div>
                                <span class="font-bold text-[14px] text-gray-900 dark:text-white/90">{{ $barber->name }}</span>
                            </div>
                            @if($barber->status)
                                <div class="border border-brand-200 text-brand-600 dark:border-brand-500/30 dark:text-brand-400 text-[10px] font-bold px-3 py-1 rounded tracking-widest uppercase">AVAILABLE</div>
                            @else
                                <div class="bg-gray-500/90 dark:bg-gray-700 text-white text-[10px] font-bold px-3 py-1 rounded tracking-widest uppercase">BUSY</div>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-6 text-gray-500 dark:text-gray-400">
                            <span class="text-sm">Belum ada data barber.</span>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
@endsection