@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Scan QR Code'" />

<div class="max-w-2xl mx-auto space-y-6">
    
    {{-- Main Scanner Box --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-800 mb-5">
            <div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-white/90">Scanner QR Code Check-in</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Arahkan kamera ke QR Code pelanggan untuk melakukan check-in</p>
            </div>
            <button onclick="startScanner()" class="rounded-xl border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 transition">
                <i class="fa-solid fa-rotate mr-1"></i> Muat Ulang Kamera
            </button>
        </div>

        {{-- Camera Viewport --}}
        <div id="qr-reader" class="mx-auto overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-900 min-h-[280px] flex items-center justify-center">
            <div class="text-center p-6 text-gray-400">
                <i class="fa-solid fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-xs">Memulai modul kamera...</p>
            </div>
        </div>

        {{-- Upload File Alternative --}}
        <div class="mt-4 flex flex-wrap items-center justify-between gap-3 p-3.5 rounded-xl border border-dashed border-gray-300 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/50">
            <div class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                <i class="fa-solid fa-image text-brand-500 text-sm"></i>
                <span>Atau scan dari foto / tangkapan layar QR Code:</span>
            </div>
            <label class="cursor-pointer inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-xs font-bold text-gray-700 border border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 transition">
                <i class="fa-solid fa-upload"></i> Pilih Foto QR
                <input type="file" id="qr-file-input" accept="image/*" class="hidden" onchange="scanImageFile(this)">
            </label>
        </div>

        {{-- Alert Info Browser Privacy / HTTPS --}}
        <div class="mt-4 rounded-xl bg-amber-50/70 p-3.5 text-xs text-amber-800 dark:bg-amber-950/30 dark:text-amber-300 border border-amber-200 dark:border-amber-900">
            <i class="fa-solid fa-circle-info mr-1.5"></i>
            <strong>Catatan Izin Kamera:</strong> Browser Safari/Chrome memerlukan izin kamera atau akses via <code>http://localhost:8000</code>. Jika webcam tidak terhubung, gunakan opsi <strong>Pilih Foto QR</strong> atau <strong>Input Manual</strong>.
        </div>

        {{-- Result Toast --}}
        <div id="qr-result" class="hidden mt-4 rounded-xl p-4 text-xs font-medium"></div>
    </div>

    {{-- Manual Input Form --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
        <h4 class="text-sm font-bold text-gray-800 dark:text-white/90 mb-2">Input Manual Kode Booking</h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Jika kamera tidak tersedia, masukkan kode booking secara manual</p>
        <div class="flex gap-3">
            <input type="text" id="manual-qr-input" placeholder="Contoh: BOOK-XXXXXXXX" 
                   class="h-11 flex-1 rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 placeholder:text-gray-400 dark:border-gray-700 dark:bg-gray-900 dark:text-white" />
            <button onclick="processCheckin(document.getElementById('manual-qr-input').value)" 
                    class="rounded-xl bg-brand-500 px-6 py-2.5 text-sm font-bold text-white shadow-theme-xs hover:bg-brand-600 transition flex items-center gap-1.5">
                <i class="fa-solid fa-check text-xs"></i> Check-in
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
    const resultDiv = document.getElementById('qr-result');
    let html5QrcodeScanner = null;

    function processCheckin(decodedText) {
        if (!decodedText) return;
        resultDiv.className = 'mt-4 rounded-xl p-4 bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400 border border-blue-200 dark:border-blue-900 text-xs font-semibold';
        resultDiv.classList.remove('hidden');
        resultDiv.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1"></i> Memproses check-in...';

        fetch("{{ route('admin.booking.checkin') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_code: decodedText.trim() }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                resultDiv.className = 'mt-4 rounded-xl p-4 bg-green-50 text-green-700 dark:bg-green-950/30 dark:text-green-400 border border-green-200 dark:border-green-900 text-xs font-semibold';
                resultDiv.innerHTML = `<div class="flex items-start gap-3">
                    <i class="fa-solid fa-circle-check text-xl text-green-600 dark:text-green-400 mt-0.5"></i>
                    <div>
                        <strong class="text-sm block mb-1">${data.message}</strong>
                        <p class="text-xs font-normal">
                            • Pelanggan: <strong>${data.booking.user?.name || 'Walk-in'}</strong><br>
                            • Barber: <strong>${data.booking.barber?.name || '-'}</strong><br>
                            • Layanan: <strong>${data.booking.layanan?.nama_layanan || '-'}</strong>
                        </p>
                    </div>
                </div>`;
            } else {
                resultDiv.className = 'mt-4 rounded-xl p-4 bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-900 text-xs font-semibold';
                resultDiv.innerHTML = `<strong>❌ ${data.message}</strong>`;
            }
            resultDiv.classList.remove('hidden');
        })
        .catch(err => {
            resultDiv.className = 'mt-4 rounded-xl p-4 bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-900 text-xs font-semibold';
            resultDiv.innerHTML = '<strong>❌ QR Code tidak valid atau booking sudah diproses.</strong>';
            resultDiv.classList.remove('hidden');
        });
    }

    function scanImageFile(input) {
        if (!input.files || !input.files[0]) return;
        const imageFile = input.files[0];
        const html5QrCode = new Html5Qrcode("qr-reader");

        html5QrCode.scanFile(imageFile, true)
            .then(decodedText => {
                processCheckin(decodedText);
            })
            .catch(err => {
                resultDiv.className = 'mt-4 rounded-xl p-4 bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400 border border-red-200 dark:border-red-900 text-xs font-semibold';
                resultDiv.innerHTML = '<strong>❌ QR Code tidak terdeteksi pada foto yang diunggah. Silakan gunakan foto yang lebih jelas.</strong>';
                resultDiv.classList.remove('hidden');
            });
    }

    function startScanner() {
        if (html5QrcodeScanner) {
            try { html5QrcodeScanner.stop(); } catch(e){}
        }

        html5QrcodeScanner = new Html5Qrcode("qr-reader");

        // Try getting available cameras first
        Html5Qrcode.getCameras().then(devices => {
            if (devices && devices.length) {
                const cameraId = devices[0].id;
                html5QrcodeScanner.start(
                    cameraId,
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    (decodedText) => {
                        html5QrcodeScanner.stop();
                        processCheckin(decodedText);
                    }
                ).catch(err => handleCameraError(err));
            } else {
                // Fallback to facingMode
                html5QrcodeScanner.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 220, height: 220 } },
                    (decodedText) => {
                        html5QrcodeScanner.stop();
                        processCheckin(decodedText);
                    }
                ).catch(err => handleCameraError(err));
            }
        }).catch(err => {
            // Try default facingMode
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: { width: 220, height: 220 } },
                (decodedText) => {
                    html5QrcodeScanner.stop();
                    processCheckin(decodedText);
                }
            ).catch(err => handleCameraError(err));
        });
    }

    function handleCameraError(err) {
        document.getElementById('qr-reader').innerHTML = `
            <div class="text-center p-6 text-gray-400">
                <i class="fa-solid fa-camera-slash text-3xl mb-2 text-amber-500"></i>
                <h5 class="text-sm font-bold text-gray-200 mb-1">Kamera Tidak Terdeteksi / Membutuhkan Izin Browser</h5>
                <p class="text-xs max-w-sm mx-auto mb-3 text-gray-400">Buka via <strong>http://localhost:8000</strong> atau berikan izin kamera pada browser Anda.</p>
                <div class="flex justify-center gap-2">
                    <button onclick="startScanner()" class="px-3.5 py-2 rounded-xl bg-brand-500 text-white text-xs font-bold hover:bg-brand-600 transition">
                        <i class="fa-solid fa-arrows-rotate mr-1"></i> Coba Lagi
                    </button>
                </div>
            </div>
        `;
    }

    document.addEventListener("DOMContentLoaded", function() {
        startScanner();
    });
</script>
@endpush
