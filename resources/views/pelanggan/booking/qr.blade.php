@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'QR Code Booking'" />
<div class="max-w-md mx-auto">
    @if(session('success'))<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400">{{ session('success') }}</div>@endif
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
