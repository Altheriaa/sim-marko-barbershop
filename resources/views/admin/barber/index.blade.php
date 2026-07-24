@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Daftar Barber'" />

@if(session('success'))
    <div class="mb-5 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 dark:bg-green-950/30 dark:text-green-400">
        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
@endif

{{-- Filter & Action Bar --}}
<div class="mb-5 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <h2 class="text-base font-bold text-gray-800 dark:text-white/90 flex items-center gap-2">
        <i class="fa-solid fa-user-group text-brand-500"></i> Tim Barber
    </h2>
    <a href="{{ route('admin.barbers.create') }}"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 shadow-theme-xs hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-brand-500 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 transition-all">
        <i class="fa-solid fa-plus text-brand-500"></i> Tambah Barber
    </a>
</div>

{{-- Table Card --}}
<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 font-semibold uppercase text-xs tracking-wider">
                    <th class="py-4 px-5">#</th>
                    <th class="py-4 px-5">Foto</th>
                    <th class="py-4 px-5">Nama Barber</th>
                    <th class="py-4 px-5">Telepon</th>
                    <th class="py-4 px-5">Status</th>
                    <th class="py-4 px-5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($barbers as $barber)
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                        <td class="py-4 px-5 text-sm text-gray-500 dark:text-gray-400">
                            {{ $loop->iteration + ($barbers->currentPage() - 1) * $barbers->perPage() }}
                        </td>
                        <td class="py-4 px-5">
                            @if($barber->photo)
                                <img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->name }}"
                                    class="h-10 w-10 rounded-xl object-cover border border-gray-200 dark:border-gray-700">
                            @else
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 font-bold text-sm dark:bg-brand-500/10 dark:text-brand-400">
                                    {{ substr($barber->name, 0, 1) }}
                                </div>
                            @endif
                        </td>
                        <td class="py-4 px-5 text-sm font-bold text-gray-900 dark:text-white">{{ $barber->name }}</td>
                        <td class="py-4 px-5 text-sm text-gray-600 dark:text-gray-400">{{ $barber->phone ?? '-' }}</td>
                        <td class="py-4 px-5">
                            <form action="{{ route('admin.barbers.toggle-status', $barber) }}" method="POST" class="inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wider transition
                                    {{ $barber->status
                                        ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $barber->status ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $barber->status ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </form>
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.barbers.edit', $barber) }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition" title="Edit Barber">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <form action="{{ route('admin.barbers.destroy', $barber) }}" method="POST"
                                    onsubmit="return confirm('Yakin hapus barber {{ $barber->name }}?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400 transition" title="Hapus Barber">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-2 text-gray-400 dark:text-gray-600">
                                <i class="fa-solid fa-user-slash text-3xl"></i>
                                <span class="text-sm">Belum ada data barber.</span>
                                <a href="{{ route('admin.barbers.create') }}" class="mt-1 text-xs font-bold text-brand-600 hover:underline dark:text-brand-400">+ Tambah Barber Baru</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="border-t border-gray-100 dark:border-gray-800 px-5 py-4">
        {{ $barbers->links() }}
    </div>
</div>
@endsection
