@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Tambah Layanan'" />
<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <form action="{{ route('admin.layanan.store') }}" method="POST">@csrf
            <div class="space-y-5">
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Layanan <span class="text-error-500">*</span></label>
                    <input type="text" name="nama_layanan" value="{{ old('nama_layanan') }}" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Cth: Potong Rambut" />
                    @error('nama_layanan') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror</div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="Deskripsi layanan (opsional)">{{ old('deskripsi') }}</textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Harga (Rp) <span class="text-error-500">*</span></label>
                        <input type="number" name="harga" value="{{ old('harga') }}" required min="0" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="50000" />
                        @error('harga') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror</div>
                    <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Durasi (menit) <span class="text-error-500">*</span></label>
                        <input type="number" name="durasi_menit" value="{{ old('durasi_menit') }}" required min="1" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" placeholder="30" />
                        @error('durasi_menit') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror</div>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">Simpan</button>
                    <a href="{{ route('admin.layanan.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
