@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Catat Pembayaran'" />
<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="mb-6 space-y-2">
            <p class="text-sm text-gray-600 dark:text-gray-400"><span class="font-medium">Kode Booking:</span> {{ $booking->qr_code }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400"><span class="font-medium">Pelanggan:</span> {{ $booking->user->name ?? 'Walk-in' }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400"><span class="font-medium">Barber:</span> {{ $booking->barber->name }}</p>
            <p class="text-sm text-gray-600 dark:text-gray-400"><span class="font-medium">Layanan:</span> {{ $booking->layanan->nama_layanan }}</p>
            <p class="text-sm text-gray-800 dark:text-white/90 font-semibold text-lg">Total: Rp {{ number_format($booking->layanan->harga, 0, ',', '.') }}</p>
        </div>
        <form action="{{ route('admin.transaksi.store', $booking) }}" method="POST">@csrf
            <div class="space-y-5">
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Metode Pembayaran <span class="text-error-500">*</span></label>
                    <select name="metode_pembayaran" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">Pilih Metode</option>
                        <option value="tunai">Tunai</option>
                        <option value="EDC">EDC</option>
                        <option value="transfer">Transfer</option>
                    </select>
                    @error('metode_pembayaran') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-green-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-green-600 transition">Konfirmasi Pembayaran</button>
                    <a href="{{ route('admin.booking.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
