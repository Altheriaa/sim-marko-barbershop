@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Scan QR Code'" />
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Scanner QR Code Check-in</h3>
        <div id="qr-reader" class="mx-auto mb-4 overflow-hidden rounded-xl" style="width: 320px;"></div>
        <div id="qr-result" class="hidden mt-4 rounded-lg p-4"></div>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">Arahkan kamera ke QR Code pelanggan untuk melakukan check-in.</p>
        </div>
    </div>

    <!-- Manual Input -->
    <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h4 class="text-md font-semibold text-gray-800 dark:text-white/90 mb-3">Input Manual Kode Booking</h4>
        <div class="flex gap-3">
            <input type="text" id="manual-qr-input" placeholder="Masukkan kode booking (BOOK-XXXXXXXX)" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 flex-1 rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            <button onclick="processCheckin(document.getElementById('manual-qr-input').value)" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">Check-in</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    const resultDiv = document.getElementById('qr-result');

    function processCheckin(decodedText) {
        if (!decodedText) return;
        resultDiv.className = 'mt-4 rounded-lg p-4 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400';
        resultDiv.classList.remove('hidden');
        resultDiv.innerHTML = 'Memproses check-in...';

        fetch("{{ route('admin.booking.checkin') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_code: decodedText }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                resultDiv.className = 'mt-4 rounded-lg p-4 bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400';
                resultDiv.innerHTML = `<strong>✅ ${data.message}</strong><br>
                    Pelanggan: ${data.booking.user?.name || 'Walk-in'}<br>
                    Barber: ${data.booking.barber.name}<br>
                    Layanan: ${data.booking.layanan.nama_layanan}`;
            } else {
                resultDiv.className = 'mt-4 rounded-lg p-4 bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400';
                resultDiv.innerHTML = `<strong>❌ ${data.message}</strong>`;
            }
            resultDiv.classList.remove('hidden');
        })
        .catch(err => {
            resultDiv.className = 'mt-4 rounded-lg p-4 bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-400';
            resultDiv.innerHTML = '<strong>❌ QR Code tidak valid atau booking sudah diproses.</strong>';
            resultDiv.classList.remove('hidden');
        });
    }

    try {
        const scanner = new Html5Qrcode("qr-reader");
        scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                scanner.stop();
                processCheckin(decodedText);
            }
        ).catch(err => {
            document.getElementById('qr-reader').innerHTML = '<p class="text-center py-8 text-sm text-gray-500">Kamera tidak tersedia. Gunakan input manual di bawah.</p>';
        });
    } catch(e) {
        document.getElementById('qr-reader').innerHTML = '<p class="text-center py-8 text-sm text-gray-500">Scanner tidak tersedia. Gunakan input manual di bawah.</p>';
    }
</script>
@endpush
