@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb :pageTitle="'Laporan & Statistik'" />

    {{-- Filter Bar --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="GET" action="{{ request()->url() }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <button type="submit"
                class="h-10 rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600 transition">
                Filter
            </button>
            <a href="{{ request()->url() }}"
                class="h-10 inline-flex items-center rounded-lg border border-gray-300 px-5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">
                Reset
            </a>
            <a href="{{ route(auth()->user()->role === 'owner' ? 'owner.laporan.cetak' : 'admin.laporan.cetak', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank"
                class="h-10 inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 text-xs font-bold text-gray-800 shadow-theme-xs hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-brand-500 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 transition-all ml-auto">
                <i class="fa-solid fa-print text-brand-500"></i> Cetak Laporan
            </a>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        {{-- Total Pendapatan --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400">
                    <i class="fa-solid fa-money-bill-wave text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</p>
            <h3 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </h3>
            <p class="mt-1 text-xs text-gray-400">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} –
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
        </div>

        {{-- Total Transaksi --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600 dark:bg-green-500/10 dark:text-green-400">
                    <i class="fa-solid fa-circle-check text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Transaksi Lunas</p>
            <h3 class="mt-1 text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($totalTransaksi) }}</h3>
            <p class="mt-1 text-xs text-gray-400">Dalam periode yang dipilih</p>
        </div>

        {{-- Layanan Terlaris --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between mb-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                    <i class="fa-solid fa-star text-xl"></i>
                </div>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Layanan Terlaris</p>
            @if($layananTerlaris && $layananTerlaris->layanan)
                <h3 class="mt-1 text-xl font-bold text-gray-800 dark:text-white/90 truncate">
                    {{ $layananTerlaris->layanan->nama_layanan }}</h3>
                <p class="mt-1 text-xs text-gray-400">{{ $layananTerlaris->total }} sesi selesai</p>
            @else
                <h3 class="mt-1 text-xl font-bold text-gray-400">-</h3>
                <p class="mt-1 text-xs text-gray-400">Belum ada data</p>
            @endif
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3 mb-6">
        {{-- Tren Pendapatan Harian --}}
        <div class="col-span-2 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Tren Pendapatan Harian</h3>
            <div class="relative" style="height: 220px;">
                <canvas id="chartTrend"></canvas>
            </div>
        </div>

        {{-- Distribusi Layanan --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Distribusi Layanan</h3>
            @forelse($distribusiLayanan as $dist)
                @php $pct = round($dist->total / $totalBookingDistribusi * 100); @endphp
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <span
                            class="text-sm text-gray-700 dark:text-gray-300 truncate max-w-[65%]">{{ $dist->layanan->nama_layanan ?? '—' }}</span>
                        <span class="text-xs font-semibold text-gray-500">{{ $pct }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-2 rounded-full bg-brand-500" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-400 text-center py-6">Belum ada data</p>
            @endforelse
        </div>
    </div>

    {{-- Pendapatan per Barber --}}
    @if($pendapatanBarber->isNotEmpty())
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white/90">Pendapatan per Barber</h3>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                @foreach($pendapatanBarber as $pb)
                    <div class="rounded-xl border border-gray-100 dark:border-gray-800 p-4 text-center">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $pb->booking->barber->name ?? '—' }}</p>
                        <p class="mt-1 text-lg font-bold text-brand-600 dark:text-brand-400">Rp
                            {{ number_format($pb->total, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Rincian Transaksi --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">Rincian Transaksi</h3>
            <span class="text-sm text-gray-400">{{ $transaksi->total() }} transaksi ditemukan</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">#
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kode
                            Booking</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Pelanggan</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Layanan</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Barber</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total
                        </th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Metode</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Tanggal Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $trx)
                        <tr
                            class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.02] transition">
                            <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration + ($transaksi->currentPage() - 1) * $transaksi->perPage() }}</td>
                            <td class="px-5 py-4 text-sm font-mono text-gray-700 dark:text-gray-300">
                                {{ $trx->booking->qr_code }}</td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 dark:bg-brand-900/30 text-xs font-bold text-brand-600 dark:text-brand-400">
                                        {{ strtoupper(substr($trx->booking->customer_name, 0, 1)) }}
                                    </div>
                                    {{ $trx->booking->customer_name }}
                                </div>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $trx->booking->layanan->nama_layanan }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $trx->booking->barber->name }}
                            </td>
                            <td class="px-5 py-4 text-sm font-semibold text-gray-800 dark:text-white/90">Rp
                                {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td class="px-5 py-4 text-sm">
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ $trx->metode_pembayaran === 'tunai' ? 'bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400' : ($trx->metode_pembayaran === 'transfer' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-400') }}">
                                    {{ ucfirst($trx->metode_pembayaran) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $trx->tanggal_bayar?->format('d/m/Y H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-12 text-center text-sm text-gray-400">Tidak ada transaksi dalam
                                periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $transaksi->links() }}</div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const labels = @json($trendHarian->pluck('tanggal')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
                const data = @json($trendHarian->pluck('total'));

                const ctx = document.getElementById('chartTrend').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: data,
                            backgroundColor: 'rgba(249, 115, 22, 0.8)',
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: '#9ca3af', font: { size: 11 } } },
                            y: {
                                grid: { color: 'rgba(156,163,175,0.15)' },
                                ticks: {
                                    color: '#9ca3af',
                                    font: { size: 11 },
                                    callback: v => 'Rp ' + (v / 1000).toFixed(0) + 'k'
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection