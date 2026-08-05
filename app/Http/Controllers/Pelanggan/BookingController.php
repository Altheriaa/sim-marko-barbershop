<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\JadwalBarber;
use App\Models\Layanan;
use App\Models\Transaksi;
use App\Services\WhatsAppService;
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

    public function create(Request $request)
    {
        $barbers = Barber::where('status', 'masuk')->get();
        $layanan = Layanan::with('subLayanan')->get();
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $jadwalList = JadwalBarber::with(['bookings.layanan', 'bookings.user'])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('barber_id');

        $initialSchedules = $barbers->map(function ($barber) use ($jadwalList) {
            $schedules = $jadwalList->get($barber->id, collect())->map(function ($j) {
                $booking = $j->bookings->first();
                return [
                    'id'          => $j->id,
                    'jam_mulai'   => Carbon::parse($j->jam_mulai)->format('H:i'),
                    'jam_selesai' => Carbon::parse($j->jam_selesai)->format('H:i'),
                    'status'      => $j->status,
                    'layanan'     => $booking?->layanan?->nama_layanan ?? 'Booked',
                ];
            });

            return [
                'id'            => $barber->id,
                'name'          => $barber->name,
                'photo_url'     => $barber->photo ? asset('storage/' . $barber->photo) : null,
                'status'        => $barber->status,
                'schedules'     => $schedules->values(),
                'total_booking' => $schedules->count(),
            ];
        });

        $formattedTanggal = Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y');

        return view('pelanggan.booking.create', compact('barbers', 'layanan', 'tanggal', 'initialSchedules', 'formattedTanggal'), ['title' => 'Buat Booking']);
    }

    public function getJadwalJson(Request $request)
    {
        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $barbers = Barber::where('status', 'masuk')->get();

        $jadwalList = JadwalBarber::with(['bookings.layanan', 'bookings.user'])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('barber_id');

        $barberSchedules = $barbers->map(function ($barber) use ($jadwalList) {
            $schedules = $jadwalList->get($barber->id, collect())->map(function ($j) {
                $activeBooking = $j->bookings->whereIn('status', ['pending', 'checked-in'])->first();
                $isPenuh = $j->status === 'penuh' && $activeBooking;
                return [
                    'id'          => $j->id,
                    'jam_mulai'   => Carbon::parse($j->jam_mulai)->format('H:i'),
                    'jam_selesai' => Carbon::parse($j->jam_selesai)->format('H:i'),
                    'status'      => $isPenuh ? 'penuh' : 'tersedia',
                    'layanan'     => $activeBooking?->layanan?->nama_layanan ?? 'Booked',
                ];
            });

            return [
                'id'            => $barber->id,
                'name'          => $barber->name,
                'photo_url'     => $barber->photo ? asset('storage/' . $barber->photo) : null,
                'status'        => $barber->status,
                'schedules'     => $schedules->values(),
                'total_booking' => $schedules->count(),
            ];
        });

        return response()->json([
            'tanggal'           => $tanggal,
            'formatted_tanggal' => Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y'),
            'barbers'           => $barberSchedules,
            'jam_operasional'   => [
                'buka'      => self::JAM_BUKA,
                'tutup'     => self::JAM_TUTUP,
                'istirahat' => self::ISTIRAHAT,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id'  => ['required', Rule::exists('barbers', 'id')->where('status', 'masuk')],
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

        // Kirim Notifikasi WhatsApp Webhook (Pelanggan & Admin)
        WhatsAppService::sendBookingConfirmation($booking);
        WhatsAppService::sendAdminBookingAlert($booking);

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

    public function invoice(Transaksi $transaksi)
    {
        if ($transaksi->booking->user_id !== auth()->id()) {
            abort(403);
        }

        $transaksi->load(['booking.layanan', 'booking.barber', 'booking.user']);
        return view('admin.transaksi.invoice', compact('transaksi'), ['title' => 'Invoice Pembayaran']);
    }
}
