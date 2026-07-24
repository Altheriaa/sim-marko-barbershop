@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Kelola Jadwal Barber'" />

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900/20 dark:text-green-400">
    <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('success') }}
</div>
@endif

{{-- Header Navigator Tanggal --}}
<div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
    <span class="text-xs uppercase font-bold tracking-widest text-gray-400 dark:text-gray-500 block mb-2">KALENDER KERJA</span>
    <div class="flex flex-wrap items-center justify-between gap-4">
        {{-- Controls Prev / Date / Next --}}
        <div class="flex items-center gap-3">
            <a href="?tanggal={{ $tanggal->copy()->subDay()->toDateString() }}&barber_id={{ $selectedBarber?->id }}" 
               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
               title="Hari Sebelumnya">
                <i class="fa-solid fa-chevron-left text-sm"></i>
            </a>

            <h2 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white font-serif">
                {{ $tanggal->locale('id')->translatedFormat('l, d F Y') }}
            </h2>

            <a href="?tanggal={{ $tanggal->copy()->addDay()->toDateString() }}&barber_id={{ $selectedBarber?->id }}" 
               class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 transition"
               title="Hari Berikutnya">
                <i class="fa-solid fa-chevron-right text-sm"></i>
            </a>
        </div>

        {{-- Date Picker Jump --}}
        <form method="GET" action="{{ route('admin.jadwal.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="barber_id" value="{{ $selectedBarber?->id }}" />
            <input type="date" name="tanggal" value="{{ $tanggal->toDateString() }}" onchange="this.form.submit()"
                   class="h-10 rounded-xl border border-gray-300 bg-transparent px-3 py-2 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            @if(!$tanggal->isToday())
            <a href="?tanggal={{ date('Y-m-d') }}&barber_id={{ $selectedBarber?->id }}" class="rounded-xl border border-brand-300 bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-700 hover:bg-brand-100 dark:bg-brand-950/40 dark:text-brand-400 transition">Hari Ini</a>
            @endif
        </form>
    </div>
</div>

{{-- Main Grid Layout --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
    
    {{-- Left Column: Tim Barber & Status Hari Ini --}}
    <div class="lg:col-span-4 space-y-6">
        
        {{-- Card: Tim Barber --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white/90 mb-4">Tim Barber</h3>
            
            <div class="space-y-3">
                @foreach($barbers as $barber)
                @php $isSelected = $selectedBarber && $selectedBarber->id === $barber->id; @endphp
                <a href="?barber_id={{ $barber->id }}&tanggal={{ $tanggal->toDateString() }}" 
                   class="flex items-center justify-between p-3.5 rounded-xl border transition {{ $isSelected ? 'border-brand-500 bg-brand-50/50 dark:bg-brand-950/20 dark:border-brand-600' : 'border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50' }}">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            @if($barber->photo)
                                <img src="{{ Storage::url($barber->photo) }}" alt="{{ $barber->name }}" class="h-11 w-11 rounded-xl object-cover">
                            @else
                                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500">
                                    <i class="fa-solid fa-user text-lg"></i>
                                </div>
                            @endif
                            <span class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900 bg-green-500"></span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white">{{ $barber->name }}</h4>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Barber</span>
                        </div>
                    </div>

                    @if($isSelected)
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-500 text-white">
                        <i class="fa-solid fa-check text-xs"></i>
                    </span>
                    @endif
                </a>
                @endforeach
            </div>

            <div class="mt-5 pt-3 border-t border-dashed border-gray-200 dark:border-gray-800">
                <a href="{{ route('admin.barbers.create') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border-2 border-dashed border-gray-300 py-3 text-xs font-bold uppercase tracking-wider text-gray-600 hover:border-brand-500 hover:text-brand-600 dark:border-gray-700 dark:text-gray-400 dark:hover:border-brand-500 transition">
                    <i class="fa-solid fa-plus"></i> Tambah Barber
                </a>
            </div>
        </div>

        {{-- Card: Status Hari Ini --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block mb-3">STATUS HARI INI</span>
            
            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-xl border border-green-200 bg-green-50/50 p-4 text-center dark:border-green-900/30 dark:bg-green-950/20">
                    <h4 class="text-3xl font-extrabold text-green-600 dark:text-green-400">{{ $totalTersedia }}</h4>
                    <span class="mt-1 block text-xs font-medium text-green-700 dark:text-green-300">Tersedia</span>
                </div>
                <div class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 text-center dark:border-amber-900/30 dark:bg-amber-950/20">
                    <h4 class="text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $totalSibuk }}</h4>
                    <span class="mt-1 block text-xs font-medium text-amber-700 dark:text-amber-300">Sibuk</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Right Column: Jadwal Shift Timeline --}}
    <div class="lg:col-span-8 space-y-6">
        
        <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
            
            {{-- Header Title & Action Buttons --}}
            <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                        <i class="fa-solid fa-clock text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">
                            Jadwal Shift: {{ $selectedBarber?->name ?? 'Pilih Barber' }}
                        </h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Shift kerja & slot pemesanan</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.jadwal.create', ['barber_id' => $selectedBarber?->id, 'tanggal' => $tanggal->toDateString()]) }}" 
                       class="inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-theme-xs hover:bg-brand-600 transition">
                        <i class="fa-solid fa-plus"></i> Tambah Shift
                    </a>
                </div>
            </div>

            {{-- Time Ruler Timeline --}}
            <div class="mt-5 mb-6 overflow-x-auto py-2 border-b border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between min-w-[500px] text-xs font-semibold text-gray-400 dark:text-gray-500">
                    <span>09:00</span>
                    <span>11:00</span>
                    <span>13:00 (Istirahat)</span>
                    <span>15:00</span>
                    <span>17:00</span>
                    <span>18:00 (Maghrib)</span>
                    <span>21:00</span>
                    <span>23:00</span>
                </div>
            </div>

            {{-- Shift Cards Timeline List --}}
            <div class="space-y-4">
                
                {{-- Fixed Rest Block 1: Makan Siang (13:00 - 14:00) --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <div>
                                <h4 class="text-base font-bold text-gray-700 dark:text-gray-300">13:00 - 14:00</h4>
                                <span class="text-xs font-medium text-gray-400">Istirahat Siang</span>
                            </div>
                            <div class="h-8 w-px bg-gray-200 dark:bg-gray-800 hidden sm:block"></div>
                            <div>
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Waktu Istirahat Makan Siang</h5>
                                <span class="text-xs text-gray-400">Rest break operasional</span>
                            </div>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-200/70 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                <i class="fa-solid fa-mug-hot text-xs"></i> Tidak Tersedia
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Fixed Rest Block 2: Maghrib (18:00 - 19:30) --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-900/50">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <div>
                                <h4 class="text-base font-bold text-gray-700 dark:text-gray-300">18:00 - 19:30</h4>
                                <span class="text-xs font-medium text-gray-400">Istirahat Maghrib</span>
                            </div>
                            <div class="h-8 w-px bg-gray-200 dark:bg-gray-800 hidden sm:block"></div>
                            <div>
                                <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Waktu Istirahat Maghrib</h5>
                                <span class="text-xs text-gray-400">Rest break operasional</span>
                            </div>
                        </div>
                        <div>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-200/70 px-3 py-1 text-xs font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                <i class="fa-solid fa-mug-hot text-xs"></i> Tidak Tersedia
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Custom Dynamic Shifts / Schedules --}}
                @forelse($jadwalList as $item)
                @php
                    $bookingInfo = $item->bookings->first();
                    $isTersedia = $item->status === 'tersedia';
                @endphp
                <div class="rounded-xl border p-4 transition shadow-xs {{ $isTersedia ? 'border-brand-200 bg-brand-50/20 dark:border-brand-900/30 dark:bg-brand-950/10' : 'border-amber-200 bg-amber-50/20 dark:border-amber-900/30 dark:bg-amber-950/10' }}">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        
                        <div class="flex items-center gap-4">
                            <div>
                                <h4 class="text-base font-bold text-gray-800 dark:text-white">{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</h4>
                                <span class="text-xs font-medium text-gray-500">
                                    @if($item->jam_mulai < '12:00') Sesi Pagi
                                    @elseif($item->jam_mulai < '18:00') Sesi Siang
                                    @else Sesi Malam @endif
                                </span>
                            </div>
                            
                            <div class="h-8 w-px bg-gray-200 dark:bg-gray-800 hidden sm:block"></div>
                            
                            <div>
                                <h5 class="text-sm font-semibold text-gray-800 dark:text-white">
                                    {{ $bookingInfo?->layanan?->nama_layanan ?? 'Slot Pangkas & Care' }}
                                </h5>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    @if($bookingInfo)
                                        Pelanggan: <strong class="text-gray-700 dark:text-gray-300">{{ $bookingInfo->user->name ?? 'Walk-in' }}</strong> ({{ ucfirst($bookingInfo->sumber) }})
                                    @else
                                        Barber: {{ $item->barber->name }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if($isTersedia)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <span class="h-2 w-2 rounded-full bg-green-500"></span> Tersedia
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                <span class="h-2 w-2 rounded-full bg-amber-500"></span> Sibuk (Terisi)
                            </span>
                            @endif

                            {{-- Actions --}}
                            <div class="flex items-center gap-1">
                                <a href="{{ route('admin.jadwal.edit', $item) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-brand-50 hover:text-brand-600 transition" title="Edit Jadwal">
                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                </a>
                                <form action="{{ route('admin.jadwal.destroy', $item) }}" method="POST" onsubmit="return confirm('Yakin hapus jadwal ini?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition" title="Hapus Jadwal">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                @empty
                <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-gray-700">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                        <i class="fa-solid fa-calendar-xmark text-xl"></i>
                    </div>
                    <h4 class="mt-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Belum ada shift jadwal khusus pada tanggal ini</h4>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Jadwal otomatis dibuat saat booking, atau Anda dapat menambah shift secara manual.</p>
                    <a href="{{ route('admin.jadwal.create', ['barber_id' => $selectedBarber?->id, 'tanggal' => $tanggal->toDateString()]) }}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold uppercase tracking-wider text-white hover:bg-brand-600 transition">
                        <i class="fa-solid fa-plus"></i> Tambah Shift Baru
                    </a>
                </div>
                @endforelse

            </div>

        </div>

    </div>

</div>
@endsection
