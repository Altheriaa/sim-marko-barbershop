@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Edit Layanan'" />
<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <form action="{{ route('admin.layanan.update', $layanan) }}" method="POST">@csrf @method('PUT')
            <div class="space-y-5">
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Layanan <span class="text-error-500">*</span></label>
                    <input type="text" name="nama_layanan" value="{{ old('nama_layanan', $layanan->nama_layanan) }}" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" /></div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('deskripsi', $layanan->deskripsi) }}</textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Harga (Rp) <span class="text-error-500">*</span></label>
                        <input type="number" name="harga" value="{{ old('harga', $layanan->harga) }}" required min="0" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" /></div>
                    <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Durasi (menit) <span class="text-error-500">*</span></label>
                        <input type="number" name="durasi_menit" value="{{ old('durasi_menit', $layanan->durasi_menit) }}" required min="1" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" /></div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Item Sub-Layanan</label>
                        <button type="button" id="btn-add-item" class="text-sm text-brand-500 hover:text-brand-600 font-medium">+ Tambah Item</button>
                    </div>
                    <div id="sub-layanan-container" class="space-y-2">
                        @foreach($layanan->subLayanan as $sub)
                        <div class="flex gap-2 items-center">
                            <input type="text" name="sub_layanan[]" value="{{ $sub->nama }}" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" placeholder="Nama item (misal: Potong Rambut)" />
                            <button type="button" class="btn-remove-item rounded-lg bg-red-50 p-2 text-red-500 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @error('sub_layanan.*') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">Perbarui</button>
                    <a href="{{ route('admin.layanan.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('sub-layanan-container');
        const btnAdd = document.getElementById('btn-add-item');

        function attachRemoveEvent(button) {
            button.addEventListener('click', function() {
                this.closest('.flex.gap-2').remove();
            });
        }

        // Attach event to existing buttons
        container.querySelectorAll('.btn-remove-item').forEach(attachRemoveEvent);

        function createItemRow(value = '') {
            const row = document.createElement('div');
            row.className = 'flex gap-2 items-center';
            row.innerHTML = `
                <input type="text" name="sub_layanan[]" value="${value}" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800" placeholder="Nama item (misal: Potong Rambut)" />
                <button type="button" class="btn-remove-item rounded-lg bg-red-50 p-2 text-red-500 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            `;
            attachRemoveEvent(row.querySelector('.btn-remove-item'));
            return row;
        }

        btnAdd.addEventListener('click', function() {
            container.appendChild(createItemRow());
        });
    });
</script>
@endsection
