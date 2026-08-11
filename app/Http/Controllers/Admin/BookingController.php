<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\Booking;
use App\Models\JadwalBarber;
use App\Models\Layanan;
use App\Models\User;
use App\Services\BookingService;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->input('periode', 'today'); // 'today', 'all'
        $barberId = $request->input('barber_id');
        $statusFilter = $request->input('status');

        $barbers = Barber::all();

        $query = Booking::with(['user', 'barber', 'layanan', 'jadwal', 'transaksi']);

        if ($periode === 'today') {
            $query->whereHas('jadwal', function ($q) {
                $q->whereDate('tanggal', now()->toDateString());
            });
        }

        if ($barberId) {
            $query->where('barber_id', $barberId);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        // Calculation metric cards
        $metricQuery = Booking::with(['layanan', 'jadwal']);
        if ($periode === 'today') {
            $metricQuery->whereHas('jadwal', function ($q) {
                $q->whereDate('tanggal', now()->toDateString());
            });
        }
        if ($barberId) {
            $metricQuery->where('barber_id', $barberId);
        }

        $allMetricBookings = $metricQuery->get();

        $totalPemesanan = $allMetricBookings->count();
        $dalamAntrean   = $allMetricBookings->whereIn('status', ['pending', 'checked-in'])->count();
        $selesai        = $allMetricBookings->where('status', 'completed')->count();
        $estimasiPendapatan = $allMetricBookings->sum(fn($b) => $b->layanan?->harga ?? 0);

        $bookings = $query->latest()->paginate(15)->withQueryString();

        return view('kasir.booking.index', compact(
            'bookings',
            'barbers',
            'periode',
            'barberId',
            'statusFilter',
            'totalPemesanan',
            'dalamAntrean',
            'selesai',
            'estimasiPendapatan'
        ), ['title' => 'Manajemen Reservasi']);
    }

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

        return view('kasir.booking.create', compact('barbers', 'layanan', 'tanggal', 'initialSchedules', 'formattedTanggal'), ['title' => 'Booking Walk-in']);
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
            'jam_operasional'   => BookingService::getJamOperasional(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_hp'          => 'nullable|string|max:20',
            'barber_id'      => ['required', Rule::exists('barbers', 'id')->where('status', 'masuk')],
            'layanan_id'     => 'required|exists:layanan,id',
            'tanggal'        => 'required|date|after_or_equal:today',
            'jam_mulai'      => 'required|date_format:H:i',
        ]);

        // Delegasi ke BookingService (termasuk validasi waktu, race condition lock)
        // Walk-in tidak kena limit max 1 booking karena user_id null
        $result = BookingService::createBooking([
            'barber_id'      => $validated['barber_id'],
            'layanan_id'     => $validated['layanan_id'],
            'tanggal'        => $validated['tanggal'],
            'jam_mulai'      => $validated['jam_mulai'],
            'user_id'        => null,
            'nama_pelanggan' => $validated['nama_pelanggan'],
            'no_hp'          => $validated['no_hp'] ?? null,
            'sumber'         => 'walk-in',
            'dibuat_oleh'    => auth()->id(),
        ]);

        if (!$result['success']) {
            return back()->withInput()->withErrors(['jam_mulai' => $result['error']]);
        }

        $booking = $result['booking'];

        // Kirim Notifikasi WhatsApp Webhook (Pelanggan & Admin)
        WhatsAppService::sendBookingConfirmation($booking);
        WhatsAppService::sendAdminBookingAlert($booking);

        return redirect()->route('kasir.booking.index')->with('success', 'Booking walk-in berhasil dibuat. Kode: ' . $booking->qr_code);
    }

    public function cancel(Booking $booking)
    {
        $result = BookingService::cancelBooking($booking);

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('kasir.booking.index')->with('success', $result['message']);
    }
}
