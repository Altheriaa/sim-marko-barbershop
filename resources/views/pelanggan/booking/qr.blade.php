@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'QR Code Booking'" />
<div class="max-w-md mx-auto">
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition.opacity.duration.500ms
            class="mb-4 flex items-center justify-between gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-800/40 shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
            <button @click="show = false" type="button" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif
    <div class="rounded-2xl border border-gray-200 bg-white p-6 text-center dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-2">QR Code Anda</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Tunjukkan QR Code ini saat tiba di barbershop untuk check-in.</p>
        <div class="inline-block rounded-xl bg-white p-4 shadow-lg">
            {!! $qrSvg !!}
        </div>
        <p class="mt-4 text-lg font-mono font-bold text-gray-800 dark:text-white/90">{{ $booking->qr_code }}</p>
        <div class="mt-6 space-y-2 text-left">
            <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">Barber</span>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $booking->barber->name }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">Layanan</span>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $booking->layanan->nama_layanan }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-100 pb-2 dark:border-gray-800">
                <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal</span>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $booking->jadwal->tanggal->format('d/m/Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">Jam</span>
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $booking->jadwal->jam_mulai }} - {{ $booking->jadwal->jam_selesai }}</span>
            </div>
        </div>
        <a href="{{ route('pelanggan.dashboard') }}" class="mt-6 inline-flex items-center rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">Kembali ke Dashboard</a>
    </div>
</div>
@endsection
