<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\JadwalBarber;
use App\Models\Layanan;
use App\Models\Transaksi;
use App\Services\BookingService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingController extends Controller
{
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
            $schedules = $jadwalList->get($barber->id, collect())
                ->filter(function ($j) {
                    $activeBooking = $j->bookings->whereIn('status', ['pending', 'checked-in'])->first();
                    return $j->status === 'penuh' && $activeBooking;
                })
                ->map(function ($j) {
                    $booking = $j->bookings->whereIn('status', ['pending', 'checked-in'])->first();
                    return [
                        'id'          => $j->id,
                        'jam_mulai'   => Carbon::parse($j->jam_mulai)->format('H:i'),
                        'jam_selesai' => Carbon::parse($j->jam_selesai)->format('H:i'),
                        'status'      => 'penuh',
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
        BookingService::autoCancelExpiredBookings();

        $tanggal = $request->input('tanggal', date('Y-m-d'));

        $barbers = Barber::where('status', 'masuk')->get();

        $jadwalList = JadwalBarber::with(['bookings.layanan', 'bookings.user'])
            ->whereDate('tanggal', $tanggal)
            ->orderBy('jam_mulai')
            ->get()
            ->groupBy('barber_id');

        $barberSchedules = $barbers->map(function ($barber) use ($jadwalList) {
            $schedules = $jadwalList->get($barber->id, collect())
                ->filter(function ($j) {
                    $activeBooking = $j->bookings->whereIn('status', ['pending', 'checked-in'])->first();
                    return $j->status === 'penuh' && $activeBooking;
                })
                ->map(function ($j) {
                    $activeBooking = $j->bookings->whereIn('status', ['pending', 'checked-in'])->first();
                    return [
                        'id'          => $j->id,
                        'jam_mulai'   => Carbon::parse($j->jam_mulai)->format('H:i'),
                        'jam_selesai' => Carbon::parse($j->jam_selesai)->format('H:i'),
                        'status'      => 'penuh',
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
            'jam_operasional'   => BookingService::getJamOperasional(),
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

        // Delegasi ke BookingService (termasuk validasi waktu, max 1 booking, race condition lock)
        $result = BookingService::createBooking([
            'barber_id'  => $validated['barber_id'],
            'layanan_id' => $validated['layanan_id'],
            'tanggal'    => $validated['tanggal'],
            'jam_mulai'  => $validated['jam_mulai'],
            'user_id'    => auth()->id(),
            'sumber'     => 'online',
        ]);

        if (!$result['success']) {
            return back()->withInput()->withErrors(['jam_mulai' => $result['error']]);
        }

        $booking = $result['booking'];

        // Kirim Notifikasi WhatsApp Webhook (Pelanggan & Admin)
        WhatsAppService::sendBookingConfirmation($booking);
        WhatsAppService::sendAdminBookingAlert($booking);

        return redirect()->route('pelanggan.booking.qr', $booking)->with('success', 'Booking berhasil! Tunjukkan QR Code saat tiba.');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        $result = BookingService::cancelBooking($booking);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('pelanggan.booking.riwayat')->with('success', $result['message']);
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
        BookingService::autoCancelExpiredBookings();

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
        return view('kasir.transaksi.invoice', compact('transaksi'), ['title' => 'Invoice Pembayaran']);
    }
}
