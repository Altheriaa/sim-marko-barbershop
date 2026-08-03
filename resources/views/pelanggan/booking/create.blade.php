@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Buat Booking'" />

@if(session('error'))
<div class="mb-4 rounded-xl bg-red-50 p-4 text-sm font-semibold text-red-600 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-900/40">
    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
    
    {{-- Form Booking Left Column --}}
    <div class="lg:col-span-6 space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100 dark:border-gray-800">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Reservasi Barbershop</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Pilih barber, layanan, tanggal & jam yang tersedia</p>
                </div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-950/50 dark:text-brand-300">
                    <span class="h-2 w-2 rounded-full bg-brand-500 animate-pulse"></span> Operational
                </span>
            </div>

            <p class="text-xs text-gray-500 dark:text-gray-400 mb-5 rounded-xl bg-gray-50 dark:bg-gray-800/50 p-3 border border-gray-100 dark:border-gray-800">
                <i class="fa-solid fa-clock text-brand-500 mr-1"></i> Jam operasional: <strong class="text-gray-700 dark:text-gray-300">10:00 – 23:00</strong><br>
                <i class="fa-solid fa-mug-hot text-amber-500 mr-1 mt-1"></i> Istirahat: <strong class="text-gray-700 dark:text-gray-300">13:00–14:00</strong> &amp; <strong class="text-gray-700 dark:text-gray-300">18:00–19:30</strong>
            </p>

            <form action="{{ route('pelanggan.booking.store') }}" method="POST">@csrf
                <div class="space-y-5">
                    
                    {{-- Pilih Barber --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Barber <span class="text-red-500">*</span></label>
                        <select name="barber_id" id="barber_id" required class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            <option value="">-- Pilih Barber --</option>
                            @foreach($barbers as $barber)
                            <option value="{{ $barber->id }}" {{ old('barber_id') == $barber->id ? 'selected' : '' }}>{{ $barber->name }}</option>
                            @endforeach
                        </select>
                        @error('barber_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Pilih Layanan --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Pilih Layanan / Paket <span class="text-red-500">*</span></label>
                        <select name="layanan_id" id="layanan_id" required class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            <option value="">-- Pilih Layanan --</option>
                            @foreach($layanan as $l)
                            <option value="{{ $l->id }}" data-durasi="{{ $l->durasi_menit }}" {{ old('layanan_id') == $l->id ? 'selected' : '' }}>
                                {{ $l->nama_layanan }} — Rp {{ number_format($l->harga, 0, ',', '.') }} ({{ $l->durasi_menit }}m)
                            </option>
                            @endforeach
                        </select>
                        @error('layanan_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal Booking <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $tanggal) }}" required min="{{ date('Y-m-d') }}"
                            class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                        @error('tanggal') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jam Mulai --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="jam_mulai" id="jam_mulai" value="{{ old('jam_mulai') }}" required min="10:00" max="23:00"
                            class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                        @error('jam_mulai') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Preview Jam Selesai --}}
                    <div id="preview-selesai" class="hidden rounded-xl bg-brand-50/70 dark:bg-brand-950/30 p-4 text-xs font-semibold text-brand-800 dark:text-brand-300 border border-brand-200 dark:border-brand-900">
                        <i class="fa-solid fa-clock text-sm mr-1"></i> Estimasi selesai: <strong id="jam-selesai-text">—</strong>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-brand-500 px-5 py-3 text-sm font-bold text-white shadow-theme-xs hover:bg-brand-600 transition">
                        <i class="fa-solid fa-check mr-1.5"></i> Konfirmasi Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Right Column: Interactive Schedule & Package Catalog --}}
    <div class="lg:col-span-6 space-y-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            
            {{-- Navigation Tabs --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-gray-200 dark:border-gray-800 mb-4">
                <div class="flex items-center gap-1.5 p-1 bg-gray-100 dark:bg-gray-800/80 rounded-xl">
                    <button type="button" id="tab-btn-jadwal" onclick="switchRightTab('jadwal')"
                        class="flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-lg transition-all text-white bg-brand-500 shadow-xs">
                        <i class="fa-solid fa-calendar-days text-sm"></i>
                        <span>Jadwal Barber</span>
                    </button>
                    <button type="button" id="tab-btn-layanan" onclick="switchRightTab('layanan')"
                        class="flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-lg transition-all text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                        <i class="fa-solid fa-scissors text-sm"></i>
                        <span>Layanan & Paket</span>
                    </button>
                </div>

                <span id="header-badge" class="rounded-lg bg-gray-100 dark:bg-gray-800 px-2.5 py-1 text-xs font-bold text-gray-600 dark:text-gray-300">
                    <i class="fa-solid fa-calendar-day text-brand-500 mr-1"></i> <span id="schedule-date-title">{{ $formattedTanggal }}</span>
                </span>
            </div>

            {{-- TAB CONTENT 1: JADWAL BARBER --}}
            <div id="tab-content-jadwal" class="space-y-4">
                
                {{-- Quick Date Selector Pills --}}
                <div class="flex items-center justify-between gap-2 bg-gray-50 dark:bg-gray-800/40 p-2.5 rounded-xl border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-1.5 overflow-x-auto text-xs scrollbar-none py-0.5">
                        <span class="font-bold text-gray-500 dark:text-gray-400 mr-1 shrink-0 text-[11px] uppercase tracking-wider hidden sm:inline">Pilihan:</span>
                        
                        <button type="button" data-date="{{ date('Y-m-d') }}" onclick="quickSelectDate('{{ date('Y-m-d') }}')"
                            class="date-pill-btn shrink-0 px-3 py-1.5 rounded-lg font-bold transition-all border text-white bg-brand-500 border-brand-500 shadow-xs">
                            Hari Ini <span class="text-[10px] opacity-80">({{ date('d/m') }})</span>
                        </button>
                        
                        <button type="button" data-date="{{ date('Y-m-d', strtotime('+1 day')) }}" onclick="quickSelectDate('{{ date('Y-m-d', strtotime('+1 day')) }}')"
                            class="date-pill-btn shrink-0 px-3 py-1.5 rounded-lg font-semibold transition-all border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-brand-400">
                            Besok <span class="text-[10px] opacity-80">({{ date('d/m', strtotime('+1 day')) }})</span>
                        </button>
                        
                        <button type="button" data-date="{{ date('Y-m-d', strtotime('+2 days')) }}" onclick="quickSelectDate('{{ date('Y-m-d', strtotime('+2 days')) }}')"
                            class="date-pill-btn shrink-0 px-3 py-1.5 rounded-lg font-semibold transition-all border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-brand-400">
                            Lusa <span class="text-[10px] opacity-80">({{ date('d/m', strtotime('+2 days')) }})</span>
                        </button>
                        
                        <button type="button" data-date="{{ date('Y-m-d', strtotime('+3 days')) }}" onclick="quickSelectDate('{{ date('Y-m-d', strtotime('+3 days')) }}')"
                            class="date-pill-btn shrink-0 px-3 py-1.5 rounded-lg font-semibold transition-all border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-brand-400">
                            +3 Hari <span class="text-[10px] opacity-80">({{ date('d/m', strtotime('+3 days')) }})</span>
                        </button>
                    </div>

                    <span id="loading-spinner" class="hidden text-brand-500 font-medium text-xs shrink-0">
                        <i class="fa-solid fa-spinner animate-spin"></i>
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 px-1">
                    <span><i class="fa-solid fa-circle-info text-brand-500 mr-1"></i> Cek jam terisi barber untuk menghindari waktu bentrok</span>
                </div>

                <div id="barber-schedules-container" class="space-y-4 max-h-[520px] overflow-y-auto pr-1">
                    {{-- Schedules rendered dynamically or using initial server state --}}
                </div>
            </div>

            {{-- TAB CONTENT 2: PILIHAN LAYANAN & PAKET --}}
            <div id="tab-content-layanan" class="hidden space-y-3.5 max-h-[580px] overflow-y-auto pr-1">
                @foreach($layanan as $l)
                <div onclick="selectLayanan({{ $l->id }})" id="layanan-card-{{ $l->id }}"
                     class="layanan-card cursor-pointer rounded-xl border border-gray-200 p-4 transition-all hover:border-brand-500 hover:shadow-xs dark:border-gray-800 dark:hover:border-brand-500">
                    
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-950/40 text-brand-600 dark:text-brand-400">
                                <i class="fa-solid fa-scissors text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">{{ $l->nama_layanan }}</h4>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $l->deskripsi }}</span>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-sm font-bold text-brand-600 dark:text-brand-400 block">Rp {{ number_format($l->harga, 0, ',', '.') }}</span>
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                <i class="fa-solid fa-clock text-[9px]"></i> {{ $l->durasi_menit }}m
                            </span>
                        </div>
                    </div>

                    @if($l->subLayanan && $l->subLayanan->count())
                    <div class="mt-3 pt-2 border-t border-gray-100 dark:border-gray-800/80">
                        <div class="flex flex-wrap gap-x-3 gap-y-1">
                            @foreach($l->subLayanan as $sub)
                            <span class="text-[11px] font-medium text-gray-600 dark:text-gray-400 inline-flex items-center gap-1">
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
    const barberSelect = document.getElementById('barber_id');
    const tanggalInput = document.getElementById('tanggal');
    const jamMulaiInput = document.getElementById('jam_mulai');
    const preview = document.getElementById('preview-selesai');
    const jamSelesaiText = document.getElementById('jam-selesai-text');
    const loadingSpinner = document.getElementById('loading-spinner');
    const schedulesContainer = document.getElementById('barber-schedules-container');
    const dateTitle = document.getElementById('schedule-date-title');

    // Initial schedules passed from Laravel controller
    let currentSchedulesData = @json($initialSchedules);

    function switchRightTab(tab) {
        const btnJadwal = document.getElementById('tab-btn-jadwal');
        const btnLayanan = document.getElementById('tab-btn-layanan');
        const contentJadwal = document.getElementById('tab-content-jadwal');
        const contentLayanan = document.getElementById('tab-content-layanan');

        if (tab === 'jadwal') {
            btnJadwal.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-lg transition-all text-white bg-brand-500 shadow-xs";
            btnLayanan.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-lg transition-all text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white";
            contentJadwal.classList.remove('hidden');
            contentLayanan.classList.add('hidden');
        } else {
            btnLayanan.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-lg transition-all text-white bg-brand-500 shadow-xs";
            btnJadwal.className = "flex items-center gap-2 px-3.5 py-2 text-xs font-bold rounded-lg transition-all text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white";
            contentLayanan.classList.remove('hidden');
            contentJadwal.classList.add('hidden');
        }
    }

    function quickSelectDate(dateStr) {
        tanggalInput.value = dateStr;
        updateActiveDatePills();
        fetchJadwalBarber();
    }

    function updateActiveDatePills() {
        const currentVal = tanggalInput.value;
        document.querySelectorAll('.date-pill-btn').forEach(btn => {
            if (btn.dataset.date === currentVal) {
                btn.className = "date-pill-btn shrink-0 px-3 py-1.5 rounded-lg font-bold transition-all border text-white bg-brand-500 border-brand-500 shadow-xs";
            } else {
                btn.className = "date-pill-btn shrink-0 px-3 py-1.5 rounded-lg font-semibold transition-all border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-brand-400";
            }
        });
    }

    function selectBarber(barberId) {
        barberSelect.value = barberId;
        highlightSelectedBarberCard();
        barberSelect.focus();
    }

    function highlightSelectedBarberCard() {
        const selectedId = barberSelect.value;
        document.querySelectorAll('.barber-schedule-card').forEach(card => {
            if (selectedId && card.dataset.barberId == selectedId) {
                card.classList.add('border-brand-500', 'ring-2', 'ring-brand-500/20', 'bg-brand-50/20');
            } else {
                card.classList.remove('border-brand-500', 'ring-2', 'ring-brand-500/20', 'bg-brand-50/20');
            }
        });
    }

    function renderSchedules(barbersData) {
        if (!barbersData || barbersData.length === 0) {
            schedulesContainer.innerHTML = `
                <div class="text-center py-8 text-gray-400 dark:text-gray-500">
                    <i class="fa-solid fa-user-slash text-3xl mb-2"></i>
                    <p class="text-sm font-medium">Tidak ada barber bertugas pada tanggal ini.</p>
                </div>
            `;
            return;
        }

        const selectedBarberId = barberSelect.value;

        let html = '';
        barbersData.forEach(b => {
            const isSelected = selectedBarberId && selectedBarberId == b.id;
            const borderClass = isSelected ? 'border-brand-500 ring-2 ring-brand-500/20 bg-brand-50/20' : 'border-gray-200 dark:border-gray-800';

            const photoHtml = b.photo_url 
                ? `<img src="${b.photo_url}" alt="${b.name}" class="h-10 w-10 rounded-xl object-cover">`
                : `<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 font-bold text-sm">${b.name.charAt(0)}</div>`;

            let slotsHtml = '';
            
            // Istirahat indicators
            const istirahatHtml = `
                <div class="inline-flex items-center gap-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50">
                    <i class="fa-solid fa-mug-hot text-[10px]"></i> 13:00–14:00 (Istirahat)
                </div>
                <div class="inline-flex items-center gap-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 px-2.5 py-1 text-[11px] font-semibold text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50">
                    <i class="fa-solid fa-mug-hot text-[10px]"></i> 18:00–19:30 (Istirahat)
                </div>
            `;

            if (b.schedules && b.schedules.length > 0) {
                b.schedules.forEach(s => {
                    slotsHtml += `
                        <div class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 dark:bg-red-950/40 px-2.5 py-1 text-[11px] font-semibold text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/50" title="${s.layanan}">
                            <i class="fa-solid fa-clock text-[10px]"></i> ${s.jam_mulai} – ${s.jam_selesai} <span class="opacity-75">(${s.layanan})</span>
                        </div>
                    `;
                });
            } else {
                slotsHtml += `
                    <div class="w-full text-[11px] font-medium text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-900/40 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-500"></i> Belum ada booking terisi. Semua jam operasional tersedia!
                    </div>
                `;
            }

            html += `
                <div data-barber-id="${b.id}" class="barber-schedule-card rounded-xl border p-4 transition-all hover:border-brand-400 dark:hover:border-brand-500 ${borderClass}">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="relative shrink-0">
                                ${photoHtml}
                                <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900 bg-emerald-500"></span>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white flex items-center gap-1.5">
                                    ${b.name}
                                </h4>
                                <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">
                                    ${b.total_booking > 0 ? `<strong class="text-brand-600 dark:text-brand-400">${b.total_booking}</strong> jadwal terisi` : 'Semua jam tersedia'}
                                </span>
                            </div>
                        </div>

                        <button type="button" onclick="selectBarber(${b.id})" 
                            class="px-3 py-1.5 text-xs font-bold rounded-lg border border-brand-500 text-brand-600 hover:bg-brand-500 hover:text-white dark:text-brand-400 dark:hover:bg-brand-500 dark:hover:text-white transition-all">
                            Pilih Barber
                        </button>
                    </div>

                    <div class="pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1.5">Status Jam Terisi & Istirahat:</span>
                        <div class="flex flex-wrap gap-1.5">
                            ${istirahatHtml}
                            ${slotsHtml}
                        </div>
                    </div>
                </div>
            `;
        });

        schedulesContainer.innerHTML = html;
    }

    async function fetchJadwalBarber() {
        const tanggalVal = tanggalInput.value;
        if (!tanggalVal) return;

        loadingSpinner.classList.remove('hidden');

        try {
            const response = await fetch(`{{ route('pelanggan.booking.jadwal-json') }}?tanggal=${tanggalVal}`);
            if (response.ok) {
                const data = await response.json();
                currentSchedulesData = data.barbers;
                dateTitle.textContent = data.formatted_tanggal;
                renderSchedules(currentSchedulesData);
            }
        } catch (e) {
            console.error('Gagal mengambil data jadwal:', e);
        } finally {
            loadingSpinner.classList.add('hidden');
        }
    }

    function updatePreview() {
        const opt = layananSelect.options[layananSelect.selectedIndex];
        const durasi = parseInt(opt?.dataset?.durasi || 0);
        const jam = jamMulaiInput.value;
        
        // Highlight active card layanan
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

    // Event Listeners
    layananSelect.addEventListener('change', updatePreview);
    jamMulaiInput.addEventListener('change', updatePreview);
    barberSelect.addEventListener('change', highlightSelectedBarberCard);
    tanggalInput.addEventListener('change', () => {
        updateActiveDatePills();
        fetchJadwalBarber();
    });

    // Initial render
    renderSchedules(currentSchedulesData);
    updateActiveDatePills();
    updatePreview();
</script>
@endsection
