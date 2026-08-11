@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb :pageTitle="'Manajemen Reservasi'" />

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3500)"
            x-transition.opacity.duration.500ms
            class="mb-5 flex items-center justify-between gap-2 rounded-xl bg-green-50 p-4 text-sm font-semibold text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-800/40 shadow-xs">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
            <button @click="show = false" type="button"
                class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
    @endif

    {{-- Header Title & Top Dropdown Filters --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold font-serif text-gray-900 dark:text-white">Manajemen Reservasi</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Daftar antrean & reservasi layanan pelanggan</p>
        </div>

        <form method="GET" action="{{ route('kasir.booking.index') }}" class="flex flex-wrap items-center gap-3">
            {{-- Filter Periode --}}
            <div class="relative">
                <select name="periode" onchange="this.form.submit()"
                    class="h-10 appearance-none rounded-xl border border-gray-300 bg-white px-4 pr-9 text-xs font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                    <option value="today" {{ $periode === 'today' ? 'selected' : '' }}> Hari Ini</option>
                    <option value="all" {{ $periode === 'all' ? 'selected' : '' }}> Semua Waktu</option>
                </select>
                <i
                    class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
            </div>

            {{-- Filter Barber --}}
            <div class="relative">
                <select name="barber_id" onchange="this.form.submit()"
                    class="h-10 appearance-none rounded-xl border border-gray-300 bg-white px-4 pr-9 text-xs font-semibold text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                    <option value=""> Semua Barber</option>
                    @foreach($barbers as $b)
                        <option value="{{ $b->id }}" {{ (string) $barberId === (string) $b->id ? 'selected' : '' }}>{{ $b->name }}
                        </option>
                    @endforeach
                </select>
                <i
                    class="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-400 pointer-events-none"></i>
            </div>
        </form>
    </div>

    {{-- 4 Stat Metric Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Card 1: TOTAL PEMESANAN --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">TOTAL
                PEMESANAN</span>
            <h3 class="mt-2 text-3xl font-extrabold text-gray-900 dark:text-white">{{ $totalPemesanan }}</h3>
        </div>

        {{-- Card 2: DALAM ANTREAN --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">DALAM
                ANTREAN</span>
            <h3 class="mt-2 text-3xl font-extrabold text-amber-600 dark:text-amber-400">{{ $dalamAntrean }}</h3>
        </div>

        {{-- Card 3: SELESAI --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">SELESAI</span>
            <h3 class="mt-2 text-3xl font-extrabold text-green-600 dark:text-green-400">{{ $selesai }}</h3>
        </div>

        {{-- Card 4: ESTIMASI PENDAPATAN --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">ESTIMASI
                PENDAPATAN</span>
            <h3 class="mt-2 text-2xl font-extrabold text-gray-900 dark:text-white">Rp.
                {{ number_format($estimasiPendapatan, 0, ',', '.') }}
            </h3>
        </div>

    </div>

    {{-- Main Reservations Timeline Section --}}
    <div class="rounded-2xl border border-gray-200 bg-gray-50/60 p-6 dark:border-gray-800 dark:bg-gray-900/40">

        {{-- Status Filter Bar / Legend --}}
        <div class="mb-6 flex flex-wrap items-center gap-4 border-b border-gray-200/80 pb-4 dark:border-gray-800">
            <a href="?periode={{ $periode }}&barber_id={{ $barberId }}"
                class="inline-flex items-center gap-2 text-xs font-semibold {{ !$statusFilter ? 'text-black font-bold underline dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400' }}">
                Semua
            </a>
            <a href="?periode={{ $periode }}&barber_id={{ $barberId }}&status=pending"
                class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $statusFilter === 'pending' ? 'text-black font-bold underline dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400' }}">
                <span class="h-2 w-2 rounded-full bg-gray-400"></span> Dipesan
            </a>
            <a href="?periode={{ $periode }}&barber_id={{ $barberId }}&status=checked-in"
                class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $statusFilter === 'checked-in' ? 'text-black font-bold underline dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400' }}">
                <span class="h-2 w-2 rounded-full bg-amber-500"></span> Check-In / Sedang Berjalan
            </a>
            <a href="?periode={{ $periode }}&barber_id={{ $barberId }}&status=completed"
                class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $statusFilter === 'completed' ? 'text-black font-bold underline dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400' }}">
                <span class="h-2 w-2 rounded-full bg-green-500"></span> Selesai
            </a>
            <a href="?periode={{ $periode }}&barber_id={{ $barberId }}&status=cancelled"
                class="inline-flex items-center gap-1.5 text-xs font-semibold {{ $statusFilter === 'cancelled' ? 'text-black font-bold underline dark:text-white' : 'text-gray-500 hover:text-gray-800 dark:text-gray-400' }}">
                <span class="h-2 w-2 rounded-full bg-red-500"></span> Dibatalkan
            </a>
        </div>

        {{-- Timeline Cards List --}}
        <div class="space-y-6">
            @forelse($bookings as $booking)
                @php
                    $jamMulai = $booking->jadwal?->jam_mulai ?? '10:00';
                    $durasi = $booking->layanan->durasi_menit ?? 30;
                    $status = $booking->status;
                    $pelangganNama = $booking->customer_name;
                @endphp

                <div class="flex flex-col sm:flex-row items-start gap-4">

                    {{-- Time Indicator Left Column --}}
                    <div class="w-24 shrink-0 pt-2">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $jamMulai }}</h4>
                        <span class="text-[11px] font-medium text-gray-400 block">{{ $durasi }} min</span>
                    </div>

                    {{-- Reservation Card Right Column --}}
                    <div
                        class="flex-1 w-full rounded-2xl border p-5 transition shadow-xs {{ $status === 'checked-in' ? 'border-gray-400 bg-gray-200/80 dark:border-gray-700 dark:bg-gray-800' : 'border-gray-200 bg-gray-200/50 dark:border-gray-800 dark:bg-gray-800/60' }}">

                        <div class="flex flex-wrap items-center justify-between gap-4">

                            {{-- Left Content: Service & Customer Info --}}
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-gray-700 shadow-xs dark:bg-gray-900 dark:text-white">
                                    <i class="fa-solid fa-scissors text-lg"></i>
                                </div>

                                <div>
                                    {{-- Status Badges Top Row --}}
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <span
                                            class="rounded bg-white/80 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-600 dark:bg-gray-900 dark:text-gray-300">
                                            {{ $booking->sumber }}
                                        </span>

                                        <span
                                            class="font-mono text-xs font-semibold text-gray-600 dark:text-gray-400 bg-white/60 dark:bg-gray-900/60 px-2 py-0.5 rounded">
                                            {{ $booking->qr_code }}
                                        </span>

                                        @if($status === 'pending')
                                            <span class="text-xs font-semibold text-amber-700 dark:text-amber-400">
                                                Dipesan (Menunggu)
                                            </span>
                                        @elseif($status === 'checked-in')
                                            <span class="text-xs font-bold text-gray-900 dark:text-white">
                                                Sedang Berjalan • Di Kursi
                                            </span>
                                        @elseif($status === 'completed')
                                            <span class="text-xs font-semibold text-green-700 dark:text-green-400">
                                                Selesai
                                            </span>
                                        @elseif($status === 'cancelled')
                                            <span class="text-xs font-semibold text-red-600 dark:text-red-400">
                                                Dibatalkan
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Service Name --}}
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $booking->layanan->nama_layanan }}
                                    </h3>

                                    {{-- Customer, Barber & Estimasi Selesai --}}
                                    <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                                        Pelanggan: <strong class="text-gray-900 dark:text-white">{{ $pelangganNama }}</strong>
                                        &bull;
                                        Barber: <strong
                                            class="text-gray-900 dark:text-white">{{ $booking->barber->name }}</strong>
                                        @if($booking->jadwal?->jam_selesai)
                                            &bull; <span class="text-gray-500">Estimasi Selesai:
                                                {{ $booking->jadwal->jam_selesai }}</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Right Content: Action Buttons & QR Modal Button --}}
                            <div class="flex flex-wrap items-center gap-2">

                                {{-- Tombol Lihat QR Code --}}
                                <button
                                    onclick="openQrModal('{{ $booking->qr_code }}', '{{ addslashes($booking->layanan->nama_layanan) }}', '{{ addslashes($pelangganNama) }}', '{{ addslashes($booking->barber->name) }}', '{{ $jamMulai }}')"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 transition">
                                    <i class="fa-solid fa-qrcode text-brand-500"></i> Lihat QR
                                </button>

                                @if($status === 'pending')
                                    <a href="{{ route('kasir.booking.scan') }}"
                                        class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 transition">
                                        <i class="fa-solid fa-qrcode mr-1"></i> Scan / Check-in
                                    </a>

                                    {{-- Tombol Batalkan Reservasi oleh Kasir --}}
                                    <form action="{{ route('kasir.booking.cancel', $booking) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN reservasi ini?\n\nSlot jadwal barber akan otomatis dibebaskan kembali.')">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-bold text-red-600 hover:bg-red-100 dark:border-red-900/40 dark:bg-red-950/30 dark:text-red-400 transition">
                                            <i class="fa-solid fa-ban mr-1"></i> Batalkan
                                        </button>
                                    </form>
                                @elseif($status === 'checked-in')
                                    <form action="{{ route('kasir.booking.checkout', $booking) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin pelayanan untuk pelanggan ini sudah SELESAI?\n\nStatus reservasi akan diubah ke Selesai dan lanjut ke catatan pembayaran.')">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-bold text-gray-800 hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition">
                                            Selesai & Bayar
                                        </button>
                                    </form>

                                    {{-- Tombol Batalkan Reservasi oleh Kasir --}}
                                    <form action="{{ route('kasir.booking.cancel', $booking) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin MEMBATALKAN reservasi ini?\n\nSlot jadwal barber akan otomatis dibebaskan kembali.')">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-xl border border-red-200 bg-red-50 px-3.5 py-2 text-xs font-bold text-red-600 hover:bg-red-100 dark:border-red-900/40 dark:bg-red-950/30 dark:text-red-400 transition">
                                            <i class="fa-solid fa-ban mr-1"></i> Batalkan
                                        </button>
                                    </form>
                                @elseif($status === 'completed')
                                    @if(!$booking->transaksi)
                                        <a href="{{ route('kasir.transaksi.create', $booking) }}"
                                            class="rounded-xl bg-brand-500 px-4 py-2 text-xs font-bold text-white hover:bg-brand-600 transition">
                                            <i class="fa-solid fa-credit-card mr-1.5"></i> Bayar
                                        </a>
                                    @endif
                                @endif

                                {{-- Tombol Cetak Struk Invoice jika transaksi sudah ada --}}
                                @if($booking->transaksi)
                                    <a href="{{ route('kasir.transaksi.invoice', $booking->transaksi) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-xs font-bold text-gray-700 hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 transition">
                                        <i class="fa-solid fa-receipt text-brand-500"></i> Struk Invoice
                                    </a>
                                @endif

                            </div>

                        </div>

                    </div>

                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-300 p-10 text-center dark:border-gray-800">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400 dark:bg-gray-800">
                        <i class="fa-solid fa-calendar-xmark text-xl"></i>
                    </div>
                    <h4 class="mt-3 text-sm font-bold text-gray-800 dark:text-white">Belum Ada Reservasi</h4>
                    <p class="mt-1 text-xs text-gray-500">Tidak ada antrean atau pemesanan pada filter ini.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">{{ $bookings->links() }}</div>

    </div>

    {{-- Modal Popup QR Code --}}
    <div id="qr-modal"
        class="fixed inset-0 z-99999 hidden flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div
            class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-center relative animate-fade-in">
            <button onclick="closeQrModal()"
                class="absolute top-4 right-4 h-8 w-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Tiket QR Code Reservasi</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4" id="modal-service-name">Layanan</p>

            {{-- Visual QR Code Image --}}
            <div class="mx-auto my-3 flex justify-center p-4 bg-white rounded-xl border border-gray-200 w-52 h-52">
                <img id="modal-qr-img" src="" alt="QR Code" class="w-full h-full object-contain">
            </div>

            <div class="mt-3 space-y-1 text-xs text-gray-700 dark:text-gray-300">
                <p>Kode: <strong class="font-mono text-brand-600 dark:text-brand-400 text-sm"
                        id="modal-qr-code">BOOK-XXXX</strong></p>
                <p>Pelanggan: <span id="modal-customer" class="font-semibold text-gray-900 dark:text-white">Walk-in</span>
                </p>
                <p>Barber: <span id="modal-barber" class="font-semibold text-gray-900 dark:text-white">-</span> &bull; Jam:
                    <span id="modal-jam" class="font-semibold">-</span>
                </p>
            </div>

            <div class="mt-5 flex gap-2">
                <button onclick="window.print()"
                    class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-black dark:bg-white dark:text-black transition">
                    <i class="fa-solid fa-print mr-1"></i> Cetak Tiket
                </button>
                <button onclick="closeQrModal()"
                    class="rounded-xl border border-gray-300 px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- Floating Circle Action Button (+) Bottom-Right --}}
    <div class="fixed bottom-8 right-8 z-50">
        <a href="{{ route('kasir.booking.create') }}"
            class="flex h-14 w-14 items-center justify-center rounded-full bg-black text-white shadow-2xl hover:bg-gray-900 dark:bg-white dark:text-black dark:hover:bg-gray-200 transition-all hover:scale-105 active:scale-95"
            title="Buat Booking Walk-in">
            <i class="fa-solid fa-plus text-xl"></i>
        </a>
    </div>

    <script>
        function openQrModal(qrCode, serviceName, customerName, barberName, jamMulai) {
            document.getElementById('modal-qr-code').textContent = qrCode;
            document.getElementById('modal-service-name').textContent = serviceName;
            document.getElementById('modal-customer').textContent = customerName;
            document.getElementById('modal-barber').textContent = barberName;
            document.getElementById('modal-jam').textContent = jamMulai;

            // Generate QR code image URL
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(qrCode)}`;
            document.getElementById('modal-qr-img').src = qrUrl;

            document.getElementById('qr-modal').classList.remove('hidden');
        }

        function closeQrModal() {
            document.getElementById('qr-modal').classList.add('hidden');
        }
    </script>

@endsection