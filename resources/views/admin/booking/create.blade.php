@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb :pageTitle="'Booking Walk-in'" />

    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-600 dark:bg-red-950/30 dark:text-red-400">
            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

        {{-- Form Booking Walk-in Left Column --}}
        <div class="lg:col-span-6 space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 mb-1">Buat Booking Walk-in</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-5">
                    Jam operasional: <strong class="text-gray-700 dark:text-gray-300">10:00 – 23:00</strong> &bull;
                    Istirahat: <strong class="text-gray-700 dark:text-gray-300">13:00–14:00</strong> &amp; <strong
                        class="text-gray-700 dark:text-gray-300">18:00–19:30</strong>
                </p>

                <form action="{{ route('admin.booking.store') }}" method="POST">@csrf
                    <div class="space-y-5">

                        {{-- Nama Pelanggan --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Nama Pelanggan
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" required
                                placeholder="Contoh: Ahmad / Budi"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                            @error('nama_pelanggan') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- No. WhatsApp --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">No. WhatsApp / HP
                                <span class="text-xs text-gray-400">(Opsional - untuk kirim bukti/struk WA)</span></label>
                            <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                                placeholder="Contoh: 081234567890"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                            @error('no_hp') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pilih Barber --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Barber
                                <span class="text-red-500">*</span></label>
                            <select name="barber_id" required
                                class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">-- Pilih Barber --</option>
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>
                                        {{ $barber->name }}</option>
                                @endforeach
                            </select>
                            @error('barber_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Pilih Layanan --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Layanan /
                                Paket <span class="text-red-500">*</span></label>
                            <select name="layanan_id" id="layanan_id" required
                                class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                <option value="">-- Pilih Layanan --</option>
                                @foreach($layanan as $l)
                                    <option value="{{ $l->id }}" data-durasi="{{ $l->durasi_menit }}" {{ old('layanan_id') == $l->id ? 'selected' : '' }}>
                                        {{ $l->nama_layanan }} — Rp {{ number_format($l->harga, 0, ',', '.') }}
                                        ({{ $l->durasi_menit }}m)
                                    </option>
                                @endforeach
                            </select>
                            @error('layanan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Booking
                                <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                                min="{{ date('Y-m-d') }}"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                            @error('tanggal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Jam Mulai --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jam Mulai <span
                                    class="text-red-500">*</span></label>
                            <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai') }}" required
                                min="10:00" max="23:00"
                                class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
                            @error('jam_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Preview Jam Selesai --}}
                        <div id="preview-selesai"
                            class="hidden rounded-xl bg-brand-50/70 dark:bg-brand-950/30 p-4 text-xs font-semibold text-brand-800 dark:text-brand-300 border border-brand-200 dark:border-brand-900">
                            <i class="fa-solid fa-clock text-sm mr-1"></i> Estimasi selesai: <strong
                                id="jam-selesai-text">—</strong>
                        </div>

                        <div class="flex gap-3 pt-2">
                            <button type="submit"
                                class="flex-1 rounded-xl bg-brand-500 px-5 py-3 text-sm font-bold text-white shadow-theme-xs hover:bg-brand-600 transition">
                                <i class="fa-solid fa-plus mr-1"></i> Buat Booking Walk-in
                            </button>
                            <a href="{{ route('admin.booking.index') }}"
                                class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column: Catalog of Available Services & Packages --}}
        <div class="lg:col-span-6 space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-800 mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">Katalog Layanan & Paket</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Klik kartu layanan untuk memilihnya langsung</p>
                    </div>
                    <span
                        class="rounded-lg bg-gray-100 dark:bg-gray-800 px-2.5 py-1 text-xs font-bold text-gray-600 dark:text-gray-300">
                        {{ $layanan->count() }} Paket
                    </span>
                </div>

                <div class="space-y-3.5 max-h-[580px] overflow-y-auto pr-1">
                    @foreach($layanan as $l)
                        <div onclick="selectLayanan({{ $l->id }})" id="layanan-card-{{ $l->id }}"
                            class="layanan-card cursor-pointer rounded-xl border border-gray-200 p-4 transition-all hover:border-brand-500 hover:shadow-xs dark:border-gray-800 dark:hover:border-brand-500">

                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-800 dark:text-white">{{ $l->nama_layanan }}</h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $l->deskripsi }}</span>
                                    </div>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-sm font-bold text-brand-600 dark:text-brand-400 block">Rp
                                        {{ number_format($l->harga, 0, ',', '.') }}</span>
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        <i class="fa-solid fa-clock text-[9px]"></i> {{ $l->durasi_menit }}m
                                    </span>
                                </div>
                            </div>

                            {{-- Items / Sub Layanan --}}
                            @if($l->subLayanan && $l->subLayanan->count())
                                <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-800/80">
                                    <div class="flex flex-wrap gap-x-3 gap-y-1">
                                        @foreach($l->subLayanan as $sub)
                                            <span
                                                class="text-[11px] font-medium text-gray-600 dark:text-gray-400 inline-flex items-center gap-1">
                                                <i class="fa-solid fa-check text-[10px] text-green-500"></i> {{ $sub->nama }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
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

            // Highlight active card
            document.querySelectorAll('.layanan-card').forEach(card => {
                card.classList.remove('border-brand-500', 'bg-brand-50/40', 'ring-2', 'ring-brand-500/20');
            });
            if (layananSelect.value) {
                const selectedCard = document.getElementById('layanan-card-' + layananSelect.value);
                if (selectedCard) {
                    selectedCard.classList.add('border-brand-500', 'bg-brand-50/40', 'ring-2', 'ring-brand-500/20');
                }
            }

            if (!durasi || !jam) { preview.classList.add('hidden'); return; }

            const [h, m] = jam.split(':').map(Number);
            const total = h * 60 + m + durasi;
            const sh = Math.floor(total / 60).toString().padStart(2, '0');
            const sm = (total % 60).toString().padStart(2, '0');
            jamSelesaiText.textContent = sh + ':' + sm + ' (durasi ' + durasi + ' menit)';
            preview.classList.remove('hidden');
        }

        function selectLayanan(id) {
            layananSelect.value = id;
            updatePreview();
        }

        layananSelect.addEventListener('change', updatePreview);
        jamMulaiInput.addEventListener('change', updatePreview);
        updatePreview();
    </script>
@endsection