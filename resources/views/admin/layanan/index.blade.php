@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Daftar Layanan'" />
@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400">{{ session('error') }}</div>
@endif
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Kelola Layanan</h3>
        <a href="{{ route('admin.layanan.create') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">+ Tambah Layanan</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Nama Layanan</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Item</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Harga</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Durasi</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($layanan as $item)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $loop->iteration + ($layanan->currentPage() - 1) * $layanan->perPage() }}</td>
                    <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $item->nama_layanan }}
                    </td>
                    <td class="px-5 py-4 text-sm">
                        @if($item->sub_layanan_count > 0)
                            <ul class="list-disc list-inside text-gray-600 dark:text-gray-400 space-y-1">
                                @foreach($item->subLayanan as $sub)
                                    <li>{{ $sub->nama }}</li>
                                @endforeach
                            </ul>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->durasi_menit }} menit</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.layanan.edit', $item) }}" class="text-brand-500 hover:text-brand-600 text-sm">Edit</a>
                            <form action="{{ route('admin.layanan.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')<button type="submit" class="text-red-500 hover:text-red-600 text-sm">Hapus</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data layanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4">{{ $layanan->links() }}</div>
</div>
@endsection
