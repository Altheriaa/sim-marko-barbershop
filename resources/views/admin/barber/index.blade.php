@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Daftar Barber'" />

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400">{{ session('success') }}</div>
@endif

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Kelola Barber</h3>
        <a href="{{ route('admin.barbers.create') }}" class="inline-flex items-center rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition">+ Tambah Barber</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">
                    <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">#</th>
                    <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Foto</th>
                    <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Nama</th>
                    <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Telepon</th>
                    <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                    <th class="px-5 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barbers as $barber)
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $loop->iteration + ($barbers->currentPage() - 1) * $barbers->perPage() }}</td>
                    <td class="px-5 py-4">
                        @if($barber->photo)
                            <img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->name }}" class="h-10 w-10 rounded-full object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-gray-500 dark:text-gray-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm font-medium text-gray-800 dark:text-white/90">{{ $barber->name }}</td>
                    <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $barber->phone ?? '-' }}</td>
                    <td class="px-5 py-4">
                        <form action="{{ route('admin.barbers.toggle-status', $barber) }}" method="POST" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $barber->status ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $barber->status ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.barbers.edit', $barber) }}" class="text-brand-500 hover:text-brand-600 text-sm">Edit</a>
                            <form action="{{ route('admin.barbers.destroy', $barber) }}" method="POST" onsubmit="return confirm('Yakin hapus barber ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-600 text-sm">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Belum ada data barber.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4">{{ $barbers->links() }}</div>
</div>
@endsection
