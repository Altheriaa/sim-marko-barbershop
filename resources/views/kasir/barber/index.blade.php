@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Daftar Barber'" />

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

{{-- Filter & Action Bar --}}
<div class="mb-5 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
    <h2 class="text-base font-bold text-gray-800 dark:text-white/90 flex items-center gap-2">
        <i class="fa-solid fa-user-group text-brand-500"></i> Tim Barber
    </h2>
    <a href="{{ route('kasir.barbers.create') }}"
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
                            <div class="flex items-center gap-2">
                                @if($barber->status === 'masuk')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-bold text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                        Masuk
                                    </span>
                                @elseif($barber->status === 'cuti')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                        Cuti
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-xs font-bold text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                        Nonaktif
                                    </span>
                                @endif

                                {{-- Quick Status Dropdown --}}
                                <div class="relative inline-block text-left" x-data="{ dropdownOpen: false }">
                                    <button @click="dropdownOpen = !dropdownOpen" type="button" class="inline-flex items-center justify-center h-7 w-7 rounded-lg border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 transition" title="Ubah Status">
                                        <i class="fa-solid fa-chevron-down text-[10px]"></i>
                                    </button>
                                    <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition class="absolute left-0 mt-1 w-32 rounded-xl border border-gray-200 bg-white p-1 shadow-lg dark:border-gray-800 dark:bg-gray-900 z-20">
                                        <form action="{{ route('kasir.barbers.toggle-status', $barber) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="masuk">
                                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-green-50 hover:text-green-700 dark:text-gray-300 dark:hover:bg-green-950/30 dark:hover:text-green-400">
                                                <span class="h-2 w-2 rounded-full bg-green-500"></span> Masuk
                                            </button>
                                        </form>
                                        <form action="{{ route('kasir.barbers.toggle-status', $barber) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cuti">
                                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-amber-50 hover:text-amber-700 dark:text-gray-300 dark:hover:bg-amber-950/30 dark:hover:text-amber-400">
                                                <span class="h-2 w-2 rounded-full bg-amber-500"></span> Cuti
                                            </button>
                                        </form>
                                        <form action="{{ route('kasir.barbers.toggle-status', $barber) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="nonaktif">
                                            <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-2.5 py-1.5 text-xs font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 dark:text-gray-300 dark:hover:bg-red-950/30 dark:hover:text-red-400">
                                                <span class="h-2 w-2 rounded-full bg-red-500"></span> Nonaktif
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-5">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('kasir.barbers.edit', $barber) }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition" title="Edit Barber">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <form action="{{ route('kasir.barbers.destroy', $barber) }}" method="POST"
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
                                <a href="{{ route('kasir.barbers.create') }}" class="mt-1 text-xs font-bold text-brand-600 hover:underline dark:text-brand-400">+ Tambah Barber Baru</a>
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
