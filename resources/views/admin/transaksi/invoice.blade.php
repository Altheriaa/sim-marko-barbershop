<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $transaksi->id }} - Marko Barbershop</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .receipt-card { box-shadow: none !important; margin: 0 auto !important; border: 1px solid #ddd !important; }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans py-8 px-4 text-gray-800 antialiased min-h-screen flex flex-col items-center justify-center">

    {{-- Floating Action Bar --}}
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-xs font-bold text-gray-800 shadow-md hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 transition-all">
            <i class="fa-solid fa-print text-brand-500"></i> Cetak Invoice Struk
        </button>
        <button onclick="window.close()" class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition">
            Tutup
        </button>
    </div>

    {{-- Thermal Receipt Card Matching Screenshot 2 --}}
    <div class="receipt-card w-full max-w-sm bg-[#faf8f5] p-6 shadow-xl rounded-xl border border-[#ede5d8] text-gray-800">
        
        {{-- Receipt Header --}}
        <div class="text-center pb-5 mb-5 border-b border-dashed border-gray-300">
            <img src="/images/logo-marko.png" alt="Marko Barbershop" class="h-10 w-auto mx-auto mb-2 object-contain" />
            <h1 class="text-xl font-bold font-serif tracking-wider text-gray-900 uppercase">MARKO BARBERSHOP</h1>
            <p class="text-xs text-gray-600 mt-1 max-w-[240px] mx-auto leading-relaxed">
                Beurawe, Kec. Kuta Alam,<br>
                Kota Banda Aceh,<br>
                Aceh 23127
            </p>
        </div>

        {{-- Order Metadata --}}
        <div class="text-xs space-y-1.5 pb-4 mb-4 border-b border-dashed border-gray-300">
            <div class="flex justify-between">
                <span class="text-gray-600">No. Resi: <strong class="text-gray-900 font-mono">#{{ str_pad($transaksi->id, 4, '0', STR_PAD_LEFT) }}</strong></span>
                <span class="text-gray-600">{{ $transaksi->tanggal_bayar->format('d.m.Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Barber: <strong class="text-gray-900">{{ $transaksi->booking->barber->name }}</strong></span>
                <span class="text-gray-600">{{ $transaksi->tanggal_bayar->format('H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Pelanggan: <strong class="text-gray-900">{{ $transaksi->booking->customer_name }}</strong></span>
                <span class="text-gray-600 uppercase font-bold text-[10px] bg-gray-200 px-1.5 py-0.5 rounded">{{ $transaksi->metode_pembayaran }}</span>
            </div>
        </div>

        {{-- Item Details --}}
        @php
            $subtotal = $transaksi->total_harga;
            $ppn = round($subtotal * 0.11);
            $total = $subtotal;
        @endphp
        <div class="text-xs pb-4 mb-4 border-b border-dashed border-gray-300">
            <div class="flex justify-between font-bold text-gray-900 text-sm mb-1">
                <span>{{ $transaksi->booking->layanan->nama_layanan }}</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="text-gray-500 text-[11px]">
                1 x Rp {{ number_format($subtotal, 0, ',', '.') }}
            </div>
        </div>

        {{-- Breakdown Totals --}}
        <div class="text-xs space-y-2 pb-3 mb-4">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal</span>
                <span class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-gray-600">
                <span>PPN (11% Termasuk)</span>
                <span class="font-semibold text-gray-900">Rp {{ number_format($ppn, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Total Line --}}
        <div class="pt-3 border-t-2 border-gray-300 flex justify-between items-center font-serif">
            <span class="text-base font-bold tracking-wider text-gray-900 uppercase">TOTAL</span>
            <span class="text-xl font-bold text-gray-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>

        {{-- Receipt Footer --}}
        <div class="mt-6 pt-4 border-t border-dashed border-gray-300 text-center text-[10px] text-gray-400">
            <p>Terima Kasih Atas Kunjungan Anda</p>
            <p class="mt-0.5">SIM Marko Barbershop</p>
        </div>

    </div>

</body>
</html>
