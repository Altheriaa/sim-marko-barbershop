<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\JadwalBarber;
use App\Models\Layanan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
    // Jam operasional & istirahat
    const JAM_BUKA   = '10:00';
    const JAM_TUTUP  = '23:00';
    const ISTIRAHAT  = [
        ['mulai' => '13:00', 'selesai' => '14:00', 'label' => 'istirahat makan siang (13:00–14:00)'],
        ['mulai' => '18:00', 'selesai' => '19:30', 'label' => 'istirahat maghrib (18:00–19:30)'],
    ];

    public function create()
    {
        $barbers = Barber::where('status', true)->get();
        $layanan = Layanan::with('subLayanan')->get();

        return view('pelanggan.booking.create', compact('barbers', 'layanan'), ['title' => 'Buat Booking']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id'  => ['required', Rule::exists('barbers', 'id')->where('status', true)],
            'layanan_id' => 'required|exists:layanan,id',
            'tanggal'    => 'required|date|after_or_equal:today',
            'jam_mulai'  => 'required|date_format:H:i',
        ]);

        $layanan    = Layanan::findOrFail($validated['layanan_id']);
        $jamMulai   = Carbon::createFromFormat('H:i', $validated['jam_mulai']);
        $jamSelesai = $jamMulai->copy()->addMinutes($layanan->durasi_menit);
        $jamMulaiStr   = $jamMulai->format('H:i');
        $jamSelesaiStr = $jamSelesai->format('H:i');

        // 1. Cek jam tidak di masa lalu jika booking untuk hari ini
        if ($validated['tanggal'] === now()->toDateString() && $jamMulaiStr < now()->format('H:i')) {
            return back()->withInput()->withErrors([
                'jam_mulai' => 'Jam mulai yang dipilih sudah lewat untuk hari ini.',
            ]);
        }

        // 2. Cek dalam jam operasional
        $buka  = Carbon::createFromFormat('H:i', self::JAM_BUKA);
        $tutup = Carbon::createFromFormat('H:i', self::JAM_TUTUP);

        if ($jamMulai->lt($buka) || $jamSelesai->gt($tutup)) {
            return back()->withInput()->withErrors([
                'jam_mulai' => 'Jam operasional barbershop adalah ' . self::JAM_BUKA . '–' . self::JAM_TUTUP . '. Pastikan jam mulai dan selesai (durasi ' . $layanan->durasi_menit . ' menit) dalam rentang tersebut.',
            ]);
        }

        // 3. Cek jam istirahat
        foreach (self::ISTIRAHAT as $istirahat) {
            $iMulai   = Carbon::createFromFormat('H:i', $istirahat['mulai']);
            $iSelesai = Carbon::createFromFormat('H:i', $istirahat['selesai']);

            if ($jamMulai->lt($iSelesai) && $jamSelesai->gt($iMulai)) {
                return back()->withInput()->withErrors([
                    'jam_mulai' => 'Jam yang dipilih bertabrakan dengan ' . $istirahat['label'] . '. Silakan pilih jam lain.',
                ]);
            }
        }

        // 4. Cek konflik booking barber di tanggal & jam yang sama
        $konflik = JadwalBarber::where('barber_id', $validated['barber_id'])
            ->where('tanggal', $validated['tanggal'])
            ->where(function ($q) use ($jamMulaiStr, $jamSelesaiStr) {
                $q->where('jam_mulai', '<', $jamSelesaiStr)
                  ->where('jam_selesai', '>', $jamMulaiStr);
            })
            ->exists();

        if ($konflik) {
            return back()->withInput()->withErrors([
                'jam_mulai' => 'Barber yang dipilih sudah memiliki booking pada jam tersebut. Pilih jam atau barber lain.',
            ]);
        }

        // 5. Auto-create jadwal & booking
        $jadwal = JadwalBarber::create([
            'barber_id'   => $validated['barber_id'],
            'tanggal'     => $validated['tanggal'],
            'jam_mulai'   => $jamMulaiStr,
            'jam_selesai' => $jamSelesaiStr,
            'status'      => 'penuh',
        ]);

        $kode = 'BOOK-' . Str::upper(Str::random(8));

        $booking = Booking::create([
            'barber_id'  => $validated['barber_id'],
            'layanan_id' => $validated['layanan_id'],
            'jadwal_id'  => $jadwal->id,
            'user_id'    => auth()->id(),
            'sumber'     => 'online',
            'qr_code'    => $kode,
            'status'     => 'pending',
        ]);

        return redirect()->route('pelanggan.booking.qr', $booking)->with('success', 'Booking berhasil! Tunjukkan QR Code saat tiba.');
    }

    public function showQr(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $qrSvg = QrCode::size(220)->generate($booking->qr_code);
        $booking->load('barber', 'layanan', 'jadwal');

        return view('pelanggan.booking.qr', compact('booking', 'qrSvg'), ['title' => 'QR Code Booking']);
    }

    public function riwayat()
    {
        $bookings = Booking::with(['barber', 'layanan', 'jadwal', 'transaksi'])
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pelanggan.booking.riwayat', compact('bookings'), ['title' => 'Riwayat Booking']);
    }
}
