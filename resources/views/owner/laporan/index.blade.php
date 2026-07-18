@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Laporan Pendapatan'" />
<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        </div>
        <button type="submit" class="h-11 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600 transition">Filter</button>
        <a href="{{ route('owner.laporan') }}" class="h-11 flex items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Reset</a>
    </form>
</div>

<div class="mb-4 rounded-2xl border border-green-200 bg-green-50 p-5 dark:border-green-800 dark:bg-green-900/20">
    <p class="text-sm text-green-600 dark:text-green-400">Total Pendapatan</p>
    <h3 class="text-2xl font-bold text-green-800 dark:text-green-300">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
</div>

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Barber</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Layanan</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Total</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Metode</th>
            </tr></thead>
            <tbody>
                @forelse($transaksi as $trx)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->tanggal_bayar?->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $trx->booking->user->name ?? 'Walk-in' }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->booking->barber->name }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->booking->layanan->nama_layanan }}</td>
                    <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($trx->metode_pembayaran) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada data transaksi untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4">{{ $transaksi->links() }}</div>
</div>
@endsection
