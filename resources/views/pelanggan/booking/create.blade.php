@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Buat Booking'" />
@if(session('error'))<div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600 dark:bg-red-900/20 dark:text-red-400">{{ session('error') }}</div>@endif
<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-1">Reservasi Barbershop</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Jam operasional: <strong class="text-gray-700 dark:text-gray-300">10:00 – 23:00</strong> &bull;
            Istirahat: <strong class="text-gray-700 dark:text-gray-300">13:00–14:00</strong> &amp;
            <strong class="text-gray-700 dark:text-gray-300">18:00–19:30</strong>
        </p>
        <form action="{{ route('pelanggan.booking.store') }}" method="POST">@csrf
            <div class="space-y-5">
                {{-- Pilih Barber --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Barber <span class="text-error-500">*</span></label>
                    <select name="barber_id" id="barber_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">-- Pilih Barber --</option>
                        @foreach($barbers as $barber)<option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>{{ $barber->name }}</option>@endforeach
                    </select>
                    @error('barber_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Pilih Layanan --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Layanan <span class="text-error-500">*</span></label>
                    <select name="layanan_id" id="layanan_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="">-- Pilih Layanan --</option>
                        @foreach($layanan as $l)<option value="{{ $l->id }}" data-durasi="{{ $l->durasi_menit }}" {{ old('layanan_id') == $l->id ? 'selected' : '' }}>{{ $l->nama_layanan }} — Rp {{ number_format($l->harga, 0, ',', '.') }} ({{ $l->durasi_menit }} menit)</option>@endforeach
                    </select>
                    @error('layanan_id') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal <span class="text-error-500">*</span></label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal') }}" required min="{{ date('Y-m-d') }}"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('tanggal') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Jam Mulai --}}
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jam Mulai <span class="text-error-500">*</span></label>
                    <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai') }}" required
                        min="10:00" max="23:00"
                        class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                    @error('jam_mulai') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Preview jam selesai --}}
                <div id="preview-selesai" class="hidden rounded-lg bg-brand-50 dark:bg-brand-900/20 px-4 py-3 text-sm text-brand-700 dark:text-brand-300">
                    Estimasi selesai: <strong id="jam-selesai-text">—</strong>
                </div>

                <button type="submit" class="w-full rounded-lg bg-brand-500 px-5 py-3 text-sm font-medium text-white hover:bg-brand-600 transition">Konfirmasi Booking</button>
            </div>
        </form>
    </div>
</div>

<script>
    const layananSelect = document.getElementById('layanan_id');
    const jamMulaiInput = document.getElementById('jam_mulai');
    const preview = document.getElementById('preview-selesai');
    const jamSelesaiText = document.getElementById('jam-selesai-text');

    function updatePreview() {
        const opt = layananSelect.options[layananSelect.selectedIndex];
        const durasi = parseInt(opt?.dataset?.durasi || 0);
        const jam = jamMulaiInput.value;
        if (!durasi || !jam) { preview.classList.add('hidden'); return; }

        const [h, m] = jam.split(':').map(Number);
        const total = h * 60 + m + durasi;
        const sh = Math.floor(total / 60).toString().padStart(2, '0');
        const sm = (total % 60).toString().padStart(2, '0');
        jamSelesaiText.textContent = sh + ':' + sm + ' (durasi ' + durasi + ' menit)';
        preview.classList.remove('hidden');
    }

    layananSelect.addEventListener('change', updatePreview);
    jamMulaiInput.addEventListener('change', updatePreview);
    updatePreview();
</script>
@endsection
