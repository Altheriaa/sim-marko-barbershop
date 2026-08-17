@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb :pageTitle="'Edit Jadwal'" />
    <div class="max-w-2xl">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <form action="{{ route('kasir.jadwal.update', $jadwal) }}" method="POST"
                data-confirm="Apakah Anda yakin ingin memperbarui jam jadwal barber ini?"
                data-confirm-title="Perbarui Jadwal"
                data-confirm-type="warning"
                data-confirm-btn="Ya, Perbarui">
                @csrf @method('PUT')
                <div class="space-y-5">
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Barber
                        </label>
                        <div class="relative">
                            <input type="text" value="{{ $jadwal->barber->name }}" disabled readonly
                                class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-400" />
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </span>
                        </div>
                        <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Barber tidak dapat diubah pada menu edit jadwal.</p>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                            Tanggal
                        </label>
                        <div class="relative">
                            <input type="text" value="{{ $jadwal->tanggal->translatedFormat('d F Y') ?? $jadwal->tanggal->format('d/m/Y') }}" disabled readonly
                                class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800/60 dark:text-gray-400" />
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400">
                                <i class="fa-solid fa-lock text-xs"></i>
                            </span>
                        </div>
                        <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Tanggal tidak dapat diubah pada menu edit jadwal.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Jam Mulai <span class="text-error-500">*</span>
                            </label>
                            <input type="time" name="jam_mulai" value="{{ old('jam_mulai', \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i')) }}" required
                                class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            @error('jam_mulai')
                                <p class="mt-1 text-xs text-error-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Estimasi Jam Selesai
                            </label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} WIB" disabled readonly
                                class="shadow-theme-xs h-11 w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-500 cursor-not-allowed dark:border-gray-700 dark:bg-gray-800/40 dark:text-gray-400" />
                            <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Otomatis disesuaikan dengan durasi layanan.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">Perbarui</button>
                        <a href="{{ route('kasir.jadwal.index', ['barber_id' => $jadwal->barber_id, 'tanggal' => $jadwal->tanggal->format('Y-m-d')]) }}"
                            class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection