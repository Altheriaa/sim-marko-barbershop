@extends('layouts.app')

@section('content')
<x-common.page-breadcrumb :pageTitle="'Kasir & Catat Pembayaran'" />

<div class="max-w-6xl mx-auto" x-data="{
    totalHarga: {{ (int) $booking->layanan->harga }},
    metode: 'tunai',
    nominalInput: '{{ (int) $booking->layanan->harga }}',
    
    get nominalBayar() {
        let val = parseInt(String(this.nominalInput).replace(/\D/g, '')) || 0;
        return val;
    },
    get kembalian() {
        if (this.metode !== 'tunai') return 0;
        return Math.max(0, this.nominalBayar - this.totalHarga);
    },
    get isKurang() {
        return this.metode === 'tunai' && this.nominalBayar < this.totalHarga;
    },
    setNominal(val) {
        this.nominalInput = val;
    },
    formatRupiah(num) {
        return new Intl.NumberFormat('id-ID').format(num || 0);
    },
    resetForm() {
        this.metode = 'tunai';
        this.nominalInput = this.totalHarga;
    }
}">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {{-- Left Column: Booking & Customer Details --}}
        <div class="lg:col-span-7 space-y-5">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-4 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500/10 text-brand-500 font-bold text-lg">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-800 dark:text-white/90">Detail Reservasi</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Informasi pelanggan dan rincian layanan</p>
                        </div>
                    </div>
                    <span class="font-mono text-xs font-bold text-brand-600 bg-brand-50 dark:bg-brand-950/50 dark:text-brand-400 px-3 py-1 rounded-full border border-brand-200 dark:border-brand-800">
                        {{ $booking->qr_code }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Customer Info --}}
                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800/60 dark:bg-gray-900/50">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Pelanggan</span>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-500 text-white text-xs font-bold shadow-theme-xs">
                                {{ strtoupper(substr($booking->customer_name, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $booking->customer_name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    <i class="fa-solid fa-phone text-[10px] mr-1"></i>{{ $booking->customer_phone ?? 'Walk-in (Tanpa No HP)' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Barber Info --}}
                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 dark:border-gray-800/60 dark:bg-gray-900/50">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-2">Barber Bertugas</span>
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-500 text-white text-xs font-bold shadow-theme-xs">
                                <i class="fa-solid fa-user-scissors"></i>
                            </div>
                            <div class="overflow-hidden">
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white truncate">{{ $booking->barber->name }}</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    <i class="fa-solid fa-clock text-[10px] mr-1"></i>Check-in: {{ $booking->waktu_checkin ? $booking->waktu_checkin->format('H:i') : '-' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Service & Pricing Summary --}}
                <div class="mt-5 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-3">Layanan Diambil</span>
                    <div class="flex items-center justify-between text-sm py-2">
                        <div>
                            <span class="font-bold text-gray-800 dark:text-white block">{{ $booking->layanan->nama_layanan }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400"><i class="fa-regular fa-clock mr-1"></i>Estimasi {{ $booking->layanan->durasi_menit ?? 30 }} Menit</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white text-base">
                            Rp {{ number_format($booking->layanan->harga, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="border-t border-dashed border-gray-200 dark:border-gray-800 mt-3 pt-3 flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-600 dark:text-gray-400">Total Yang Harus Dibayar</span>
                        <span class="text-lg font-black text-brand-600 dark:text-brand-400">
                            Rp {{ number_format($booking->layanan->harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Back button link --}}
                <div class="mt-5">
                    <a href="{{ route('kasir.booking.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white transition">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Booking
                    </a>
                </div>
            </div>
        </div>

        {{-- Right Column: Kasir Cashier Card (Aligned to Website Theme) --}}
        <div class="lg:col-span-5">
            <form action="{{ route('kasir.transaksi.store', $booking) }}" method="POST">
                @csrf
                <input type="hidden" name="metode_pembayaran" :value="metode">

                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs overflow-hidden">
                    
                    {{-- Header Banner Kasir (Brand Theme) --}}
                    <div class="bg-gradient-to-r from-brand-600 to-brand-500 px-6 py-4 text-white flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-solid fa-cash-register text-xl"></i>
                            <h3 class="text-lg font-bold">Ringkasan Transaksi</h3>
                        </div>
                        <span class="text-[11px] font-semibold bg-brand-700/60 px-2.5 py-1 rounded-full border border-brand-400/30">
                            Marko POS
                        </span>
                    </div>

                    <div class="p-6 space-y-5">

                        {{-- Total Harga Display --}}
                        <div class="text-center sm:text-right">
                            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Total Harga</span>
                            <div class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                                Rp {{ number_format($booking->layanan->harga, 0, ',', '.') }}
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-800" />

                        {{-- Pilih Metode Pembayaran --}}
                        <div>
                            <label class="text-xs font-bold text-gray-700 dark:text-gray-300 block mb-2">
                                Pilih Metode Pembayaran <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-2.5">
                                {{-- Button Tunai --}}
                                <button type="button" @click="metode = 'tunai'; nominalInput = totalHarga;" 
                                    :class="metode === 'tunai' 
                                        ? 'border-brand-500 bg-brand-50 text-brand-700 dark:bg-brand-950/40 dark:text-brand-300 ring-2 ring-brand-500/20' 
                                        : 'border-gray-200 bg-white text-gray-700 dark:border-gray-800 dark:bg-gray-900/60 dark:text-gray-300 hover:bg-gray-50'"
                                    class="flex items-center justify-center gap-2 p-3 rounded-xl border font-bold text-xs transition cursor-pointer">
                                    <i class="fa-solid fa-money-bill-wave text-brand-500 text-sm"></i>
                                    <span>Tunai</span>
                                </button>

                                {{-- Button QRIS / Transfer --}}
                                <button type="button" @click="metode = 'transfer'; nominalInput = totalHarga;" 
                                    :class="(metode === 'transfer' || metode === 'qris') 
                                        ? 'border-brand-500 bg-brand-50 text-brand-700 dark:bg-brand-950/40 dark:text-brand-300 ring-2 ring-brand-500/20' 
                                        : 'border-gray-200 bg-white text-gray-700 dark:border-gray-800 dark:bg-gray-900/60 dark:text-gray-300 hover:bg-gray-50'"
                                    class="flex items-center justify-center gap-2 p-3 rounded-xl border font-bold text-xs transition cursor-pointer">
                                    <i class="fa-solid fa-qrcode text-brand-500 text-sm"></i>
                                    <span>QRIS / Transfer</span>
                                </button>
                            </div>
                        </div>

                        {{-- Section Mode Tunai --}}
                        <div x-show="metode === 'tunai'" x-transition class="space-y-4 pt-1">
                            
                            {{-- Input Jumlah Bayar --}}
                            <div>
                                <label class="text-xs font-bold text-gray-700 dark:text-gray-300 block mb-1.5">
                                    Jumlah Bayar
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">Rp</span>
                                    <input type="number" 
                                           x-model="nominalInput" 
                                           placeholder="Masukkan nominal bayar" 
                                           class="h-11 w-full rounded-xl border border-gray-300 bg-transparent pl-10 pr-4 text-sm font-bold text-gray-900 placeholder:text-gray-400 focus:border-brand-500 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                           required />
                                </div>

                                {{-- Quick Money Preset Buttons --}}
                                <div class="mt-2.5 flex flex-wrap gap-1.5">
                                    <button type="button" @click="setNominal(totalHarga)" 
                                            class="px-2.5 py-1 rounded-lg border border-gray-200 bg-white text-[11px] font-bold text-gray-700 hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-brand-700 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 transition">
                                        Uang Pas
                                    </button>
                                    <template x-for="preset in [50000, 100000, 150000, 200000]">
                                        <button type="button" 
                                                x-show="preset >= totalHarga"
                                                @click="setNominal(preset)" 
                                                class="px-2.5 py-1 rounded-lg border border-gray-200 bg-white text-[11px] font-bold text-gray-700 hover:border-brand-300 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-brand-700 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 transition"
                                                x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(preset)">
                                        </button>
                                    </template>
                                </div>
                            </div>

                            {{-- Output Kembalian --}}
                            <div class="rounded-xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-800 dark:bg-gray-900/50">
                                <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Kembalian</span>
                                <div class="text-2xl sm:text-3xl font-extrabold"
                                     :class="isKurang ? 'text-red-500' : 'text-emerald-600 dark:text-emerald-400'">
                                    Rp <span x-text="formatRupiah(kembalian)"></span>
                                </div>
                                <p x-show="isKurang" class="text-xs font-semibold text-red-500 mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> Nominal bayar kurang dari total harga!
                                </p>
                            </div>

                        </div>

                        {{-- Section Mode QRIS / Transfer --}}
                        <div x-show="metode === 'transfer' || metode === 'qris'" x-transition class="space-y-3 pt-1">
                            <div class="rounded-xl border border-brand-200 bg-brand-50/50 p-4 text-center dark:border-brand-900/50 dark:bg-brand-950/30">
                                <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-500/10 text-brand-600 dark:text-brand-400 mb-2 text-lg font-bold">
                                    <i class="fa-solid fa-qrcode"></i>
                                </div>
                                <h5 class="text-xs font-bold text-brand-900 dark:text-brand-200">QRIS / Bank Transfer Marko Barbershop</h5>
                                <p class="text-[11px] text-brand-700 dark:text-brand-300 mt-0.5">BCA: <strong>827-0918-291</strong> a.n Marko Barbershop</p>

                                {{-- Placeholder Graphic QR Code --}}
                                <div class="mt-3 mx-auto w-28 h-28 bg-white p-2 rounded-xl shadow-xs border border-brand-200 flex items-center justify-center">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=MARKO-BARBERSHOP-PAYMENT-{{ $booking->id }}" 
                                         alt="QRIS Marko Barbershop" 
                                         class="w-full h-full object-contain" />
                                </div>
                                <span class="inline-block mt-2 text-[10px] font-semibold text-brand-600 dark:text-brand-400 bg-brand-100 dark:bg-brand-900/50 px-2 py-0.5 rounded">
                                    Verifikasi otomatis oleh Kasir
                                </span>
                            </div>
                        </div>

                        <hr class="border-gray-200 dark:border-gray-800" />

                        {{-- Action Buttons --}}
                        <div class="space-y-2.5 pt-1">
                            {{-- Submit Button --}}
                            <button type="submit" 
                                    :disabled="isKurang"
                                    class="w-full py-3.5 px-4 rounded-xl bg-brand-500 hover:bg-brand-600 text-white font-extrabold text-sm sm:text-base shadow-theme-xs transition flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fa-solid fa-circle-check text-base"></i>
                                <span>Proses Transaksi</span>
                            </button>

                            {{-- Reset Button --}}
                            <button type="button" 
                                    @click="resetForm()" 
                                    class="w-full py-2.5 px-4 rounded-xl border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 font-bold text-xs shadow-theme-xs transition flex items-center justify-center gap-2 cursor-pointer">
                                <i class="fa-solid fa-rotate-left"></i>
                                <span>Reset Input</span>
                            </button>
                        </div>

                    </div>

                </div>
            </form>
        </div>

    </div>
</div>
@endsection
