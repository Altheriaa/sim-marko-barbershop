@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Booking Walk-in'" />
<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <form action="{{ route('admin.booking.store') }}" method="POST">@csrf
            <div class="space-y-5">
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Barber <span class="text-error-500">*</span></label>
                    <select name="barber_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Pilih Barber</option>
                        @foreach($barbers as $barber)<option value="{{ $barber->id }}">{{ $barber->name }}</option>@endforeach
                    </select></div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Layanan <span class="text-error-500">*</span></label>
                    <select name="layanan_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Pilih Layanan</option>
                        @foreach($layanan as $l)<option value="{{ $l->id }}">{{ $l->nama_layanan }} - Rp {{ number_format($l->harga, 0, ',', '.') }}</option>@endforeach
                    </select></div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jadwal <span class="text-error-500">*</span></label>
                    <select name="jadwal_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Pilih Jadwal</option>
                        @foreach($jadwal as $j)<option value="{{ $j->id }}">{{ $j->barber->name }} — {{ $j->tanggal->format('d/m/Y') }} {{ $j->jam_mulai }}-{{ $j->jam_selesai }}</option>@endforeach
                    </select></div>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">Buat Booking</button>
                    <a href="{{ route('admin.booking.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
