@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Buat Booking'" />
@if(session('error'))<div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400">{{ session('error') }}</div>@endif
<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-5">Reservasi Barbershop</h3>
        <form action="{{ route('pelanggan.booking.store') }}" method="POST">@csrf
            <div class="space-y-5">
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Barber <span class="text-error-500">*</span></label>
                    <select name="barber_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">-- Pilih Barber --</option>
                        @foreach($barbers as $barber)<option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>{{ $barber->name }}</option>@endforeach
                    </select>@error('barber_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror</div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Layanan <span class="text-error-500">*</span></label>
                    <select name="layanan_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">-- Pilih Layanan --</option>
                        @foreach($layanan as $l)<option value="{{ $l->id }}" {{ old('layanan_id') == $l->id ? 'selected' : '' }}>{{ $l->nama_layanan }} — Rp {{ number_format($l->harga, 0, ',', '.') }} ({{ $l->durasi_menit }} menit)</option>@endforeach
                    </select>@error('layanan_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror</div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Jadwal <span class="text-error-500">*</span></label>
                    <select name="jadwal_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">-- Pilih Jadwal --</option>
                        @foreach($jadwal as $j)<option value="{{ $j->id }}" {{ old('jadwal_id') == $j->id ? 'selected' : '' }}>{{ $j->barber->name }} — {{ $j->tanggal->format('d/m/Y') }} pukul {{ $j->jam_mulai }} - {{ $j->jam_selesai }}</option>@endforeach
                    </select>
                    @error('jadwal_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    @if($jadwal->isEmpty())<p class="mt-1 text-sm text-yellow-600 dark:text-yellow-400">Tidak ada jadwal tersedia saat ini.</p>@endif</div>
                <button type="submit" class="w-full rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white hover:bg-brand-600 transition" {{ $jadwal->isEmpty() ? 'disabled' : '' }}>Konfirmasi Booking</button>
            </div>
        </form>
    </div>
</div>
@endsection
