@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Dashboard Owner'" />
<div class="grid grid-cols-1 gap-4 md:gap-6 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 dark:bg-green-500/10">
                <svg class="text-green-500" width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1.41 16.09V20h-2.67v-1.93c-1.71-.36-3.16-1.46-3.27-3.4h1.96c.1 1.05.82 1.87 2.65 1.87 1.96 0 2.4-.98 2.4-1.59 0-.83-.44-1.61-2.67-2.14-2.48-.6-4.18-1.62-4.18-3.67 0-1.72 1.39-2.84 3.11-3.21V4h2.67v1.95c1.86.45 2.79 1.86 2.85 3.39H14.3c-.05-1.11-.64-1.87-2.22-1.87-1.5 0-2.4.68-2.4 1.64 0 .84.65 1.39 2.67 1.94s4.18 1.36 4.18 3.85c0 1.89-1.44 2.98-3.12 3.19z"/></svg>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Pendapatan Bulan Ini</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h4>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/10">
                <svg class="text-brand-500" width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M4 10h3v7H4zm6.5-5h3v12h-3zM17 7h3v10h-3z"/></svg>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Total Booking</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $totalBooking }}</h4>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-500/10">
                <svg class="text-purple-500" width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm text-gray-500 dark:text-gray-400">Booking Bulan Ini</span>
                <h4 class="mt-2 text-title-sm font-bold text-gray-800 dark:text-white/90">{{ $bookingBulanIni }}</h4>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-500/10">
                <svg class="text-orange-500" width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z"/></svg>
            </div>
        </div>
    </div>
</div>
<div class="mt-6 text-center">
    <a href="{{ route('owner.laporan') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600 transition">Lihat Laporan Lengkap →</a>
</div>
@endsection
