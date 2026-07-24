<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Performa Bisnis - Marko Barbershop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; color: black !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; margin: 0 !important; max-width: 100% !important; border: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans py-8 px-4 text-gray-800 antialiased min-h-screen flex flex-col items-center justify-start">

    {{-- Floating Action Bar --}}
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-5 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-lg hover:bg-black transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Laporan
        </button>
        <button onclick="window.close()" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
            Tutup
        </button>
    </div>

    {{-- Main Printable Document Paper --}}
    <div class="print-container w-full max-w-4xl bg-white p-10 md:p-12 shadow-2xl rounded-2xl border border-gray-200">
        
        {{-- Document Header --}}
        <div class="flex flex-wrap items-start justify-between gap-6 pb-6 border-b border-gray-200">
            <div class="flex items-center gap-4">
                <img src="/images/logo-marko.png" alt="Marko Barbershop" class="h-14 w-auto object-contain" />
                <div>
                    <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight">Laporan Performa Bisnis</h1>
                    <p class="text-sm font-serif text-gray-500">Marko Barbershop</p>
                </div>
            </div>

            <div class="text-right">
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block mb-1">PERIODE LAPORAN</span>
                <h4 class="text-sm font-bold text-gray-900">
                    {{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y') }} - 
                    {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y') }}
                </h4>
                <p class="text-xs text-gray-400 mt-1">Dicetak pada: {{ now()->locale('id')->translatedFormat('j M Y') }}</p>
            </div>
        </div>

        {{-- Table Data Transaksi --}}
        <div class="my-8 overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-[#e9e3d8] border-b-2 border-gray-300 text-gray-700 uppercase tracking-wider font-bold">
                        <th class="py-3 px-4">ID Transaksi</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Layanan</th>
                        <th class="py-3 px-4">Barber</th>
                        <th class="py-3 px-4 text-right">Total</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transaksi as $t)
                    <tr class="hover:bg-gray-50/80">
                        <td class="py-4 px-4 font-bold text-gray-900 font-mono">#MB-{{ $t->tanggal_bayar->format('Y') }}-{{ str_pad($t->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-4 px-4 font-semibold text-gray-800">{{ $t->booking->user->name ?? 'Walk-in' }}</td>
                        <td class="py-4 px-4 text-gray-700">{{ $t->booking->layanan->nama_layanan }}</td>
                        <td class="py-4 px-4 text-gray-700">{{ $t->booking->barber->name }}</td>
                        <td class="py-4 px-4 text-right font-bold text-gray-900">Rp {{ number_format($t->total_harga, 0, ',', '.') }}</td>
                        <td class="py-4 px-4 text-center">
                            <span class="inline-block rounded bg-gray-200 border border-gray-300 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-gray-700">SUKSES</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-500 font-medium">Tidak ada data transaksi lunas pada periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Footer Signatures & Page Indicator --}}
        <div class="mt-16 pt-8 border-t border-gray-100 flex flex-wrap items-end justify-between text-xs text-gray-600">
            <div>
                <p class="text-gray-400">Halaman 1 dari 1</p>
            </div>

            <div class="text-center w-56">
                <p class="font-bold text-gray-800 mb-16">Disetujui Oleh,</p>
                <div class="border-b border-gray-400 mb-1 w-48 mx-auto"></div>
                <p class="font-semibold text-gray-700">Manajer Toko</p>
            </div>
        </div>

    </div>

</body>
</html>
