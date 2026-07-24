@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Dashboard Owner'" />

{{-- Metric Cards --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4 mb-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</span>
                <h4 class="mt-1 text-xl font-bold text-gray-800 dark:text-white/90">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400">
                <i class="fa-solid fa-money-bill-wave text-lg"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Pendapatan Bulan Ini</span>
                <h4 class="mt-1 text-xl font-bold text-brand-600 dark:text-brand-400">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                <i class="fa-solid fa-chart-line text-lg"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Booking</span>
                <h4 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ $totalBooking }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400">
                <i class="fa-solid fa-calendar-days text-lg"></i>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Booking Bulan Ini</span>
                <h4 class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $bookingBulanIni }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                <i class="fa-solid fa-calendar-plus text-lg"></i>
            </div>
        </div>
    </div>
</div>

{{-- Quick Action --}}
<div class="mt-2">
    <a href="{{ route('owner.laporan') }}"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-xs font-bold text-gray-800 shadow-theme-xs hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-brand-500 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 transition-all">
        <i class="fa-solid fa-chart-bar text-brand-500"></i> Lihat Laporan Lengkap
    </a>
</div>
@endsection
