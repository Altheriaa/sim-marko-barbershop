@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb :pageTitle="'Daftar Layanan'" />

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
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)" x-transition.opacity.duration.500ms
            class="mb-5 flex items-center justify-between gap-2 rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-800/40 shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark"></i> {{ session('error') }}
            </div>
            <button @click="show = false" type="button" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- Filter & Action Bar --}}
    <div
        class="mb-5 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <h2 class="text-base font-bold text-gray-800 dark:text-white/90 flex items-center gap-2">
            <i class="fa-solid fa-scissors text-brand-500"></i> Daftar Layanan Aktif
        </h2>
        <a href="{{ route('admin.layanan.create') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 shadow-theme-xs hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-brand-500 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 transition-all">
            <i class="fa-solid fa-plus text-brand-500"></i> Tambah Layanan
        </a>
    </div>

    {{-- Table Card --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 font-semibold uppercase text-xs tracking-wider">
                        <th class="py-4 px-5">#</th>
                        <th class="py-4 px-5">Nama Layanan</th>
                        <th class="py-4 px-5">Item / Sub-Layanan</th>
                        <th class="py-4 px-5">Harga</th>
                        <th class="py-4 px-5">Durasi</th>
                        <th class="py-4 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($layanan as $item)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                            <td class="py-4 px-5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration + ($layanan->currentPage() - 1) * $layanan->perPage() }}
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="font-bold text-sm text-gray-900 dark:text-white">{{ $item->nama_layanan }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-5 text-sm text-gray-600 dark:text-gray-400">
                                @if($item->sub_layanan_count > 0)
                                    <ul class="space-y-0.5">
                                        @foreach($item->subLayanan as $sub)
                                            <li class="flex items-center gap-1.5">
                                                <span class="h-1.5 w-1.5 rounded-full bg-brand-400"></span>
                                                {{ $sub->nama }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-sm font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($item->harga, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-5">
                                <span
                                    class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    <i class="fa-regular fa-clock text-xs"></i>
                                    {{ $item->durasi_menit }} menit
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.layanan.edit', $item) }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-600 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition"
                                        title="Edit Layanan">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    <form action="{{ route('admin.layanan.destroy', $item) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus layanan ini?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400 transition"
                                            title="Hapus Layanan">
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
                                    <i class="fa-solid fa-scissors text-3xl"></i>
                                    <span class="text-sm">Belum ada data layanan.</span>
                                    <a href="{{ route('admin.layanan.create') }}"
                                        class="mt-1 text-xs font-bold text-brand-600 hover:underline dark:text-brand-400">+
                                        Tambah Layanan Baru</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-gray-100 dark:border-gray-800 px-5 py-4">
            {{ $layanan->links() }}
        </div>
    </div>
@endsection