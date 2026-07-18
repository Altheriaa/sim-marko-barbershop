@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Daftar Transaksi'" />
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat Transaksi</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Kode Booking</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Layanan</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Total</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Metode</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
            </tr></thead>
            <tbody>
                @forelse($transaksi as $trx)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}</td>
                    <td class="px-5 py-4 text-sm font-mono text-gray-800 dark:text-white/90">{{ $trx->booking->qr_code }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->booking->user->name ?? 'Walk-in' }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->booking->layanan->nama_layanan }}</td>
                    <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($trx->metode_pembayaran) }}</td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $trx->status_pembayaran === 'lunas' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">{{ ucfirst($trx->status_pembayaran) }}</span></td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->tanggal_bayar?->format('d/m/Y H:i') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4">{{ $transaksi->links() }}</div>
</div>
@endsection
