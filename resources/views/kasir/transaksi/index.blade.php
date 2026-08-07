@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Riwayat Transaksi'" />

@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition.opacity.duration.500ms
        class="mb-5 flex items-center justify-between gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-800/40 shadow-xs">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
        <button @click="show = false" type="button" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>
@endif

{{-- Metric Cards --}}
<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pendapatan</span>
                <h4 class="mt-1 text-xl font-bold text-gray-900 dark:text-white/90">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                <i class="fa-solid fa-money-bill-wave text-lg"></i>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transaksi</span>
                <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white/90">{{ number_format($totalTransaksi) }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                <i class="fa-solid fa-receipt text-lg"></i>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Transaksi Hari Ini</span>
                <h4 class="mt-1 text-2xl font-bold text-green-600 dark:text-green-400">{{ $transaksiHariIni }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400">
                <i class="fa-solid fa-calendar-check text-lg"></i>
            </div>
        </div>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pendapatan Hari Ini</span>
                <h4 class="mt-1 text-lg font-bold text-amber-600 dark:text-amber-400">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</h4>
            </div>
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400">
                <i class="fa-solid fa-chart-line text-lg"></i>
            </div>
        </div>
    </div>
</div>

{{-- Filter & Search Bar --}}
<div class="mb-5 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative min-w-[260px]">
                <span class="absolute -translate-y-1/2 pointer-events-none left-3.5 top-1/2 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-sm"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode, pelanggan, layanan..."
                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-9 pr-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <select name="metode" onchange="this.form.submit()"
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">Semua Metode</option>
                <option value="tunai" {{ $metode === 'tunai' ? 'selected' : '' }}>Tunai</option>
                <option value="EDC" {{ $metode === 'EDC' ? 'selected' : '' }}>EDC</option>
                <option value="transfer" {{ $metode === 'transfer' ? 'selected' : '' }}>Transfer</option>
            </select>
            <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-semibold text-white hover:bg-brand-600 transition">
                Filter
            </button>
            @if($search || $metode)
                <a href="{{ request()->url() }}" class="h-10 inline-flex items-center rounded-lg border border-gray-300 px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 transition">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Table Card --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 font-semibold uppercase text-xs tracking-wider">
                    <th class="py-4 px-5">#</th>
                    <th class="py-4 px-5">Kode Booking</th>
                    <th class="py-4 px-5">Pelanggan</th>
                    <th class="py-4 px-5">Layanan</th>
                    <th class="py-4 px-5">Barber</th>
                    <th class="py-4 px-5">Total</th>
                    <th class="py-4 px-5">Metode</th>
                    <th class="py-4 px-5">Status</th>
                    <th class="py-4 px-5">Tanggal Bayar</th>
                    <th class="py-4 px-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($transaksi as $trx)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                        <td class="py-4 px-5 text-sm text-gray-500 dark:text-gray-400">
                            {{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}
                        </td>
                        <td class="py-4 px-5 font-mono text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $trx->booking->qr_code }}
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-2">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/30 text-xs font-bold text-brand-600 dark:text-brand-400">
                                    {{ strtoupper(substr($trx->booking->customer_name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $trx->booking->customer_name }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-5 text-sm text-gray-800 dark:text-gray-200">{{ $trx->booking->layanan->nama_layanan }}</td>
                        <td class="py-4 px-5 text-sm text-gray-600 dark:text-gray-400">{{ $trx->booking->barber->name }}</td>
                        <td class="py-4 px-5 text-sm font-bold text-gray-900 dark:text-white">
                            Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-5">
                            @php
                                $metodeColors = [
                                    'tunai'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'EDC'      => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    'transfer' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                ];
                            @endphp
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $metodeColors[$trx->metode_pembayaran] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($trx->metode_pembayaran) }}
                            </span>
                        </td>
                        <td class="py-4 px-5">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold
                                {{ $trx->status_pembayaran === 'lunas' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $trx->status_pembayaran === 'lunas' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                {{ ucfirst($trx->status_pembayaran) }}
                            </span>
                        </td>
                        <td class="py-4 px-5 text-sm text-gray-600 dark:text-gray-400">
                            {{ $trx->tanggal_bayar?->translatedFormat('d M Y, H:i') ?? '-' }}
                        </td>
                        <td class="py-4 px-5 text-center">
                            <a href="{{ route(auth()->user()->role === 'owner' ? 'owner.transaksi.invoice' : 'kasir.transaksi.invoice', $trx) }}" target="_blank"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
                                <i class="fa-solid fa-receipt text-brand-500"></i> Struk
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-600">
                                <i class="fa-solid fa-receipt text-3xl"></i>
                                <span class="text-sm">Belum ada data transaksi.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-gray-100 dark:border-gray-800 px-5 py-4">
        {{ $transaksi->links() }}
    </div>
</div>
@endsection