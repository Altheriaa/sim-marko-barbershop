@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Daftar Jadwal Barber'" />
@if(session('success'))<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400">{{ session('success') }}</div>@endif
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Jadwal Barber</h3>
        <a href="{{ route('admin.jadwal.create') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">+ Tambah Jadwal</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead><tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Barber</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Jam</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
            </tr></thead>
            <tbody>
                @forelse($jadwal as $item)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $loop->iteration + ($jadwal->currentPage() - 1) * $jadwal->perPage() }}</td>
                    <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $item->barber->name }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->tanggal->format('d/m/Y') }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $item->status === 'tersedia' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">{{ ucfirst($item->status) }}</span></td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.jadwal.edit', $item) }}" class="text-brand-500 hover:text-brand-600 text-sm">Edit</a>
                            <form action="{{ route('admin.jadwal.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin hapus?')">@csrf @method('DELETE')<button type="submit" class="text-red-500 hover:text-red-600 text-sm">Hapus</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada jadwal.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4">{{ $jadwal->links() }}</div>
</div>
@endsection
