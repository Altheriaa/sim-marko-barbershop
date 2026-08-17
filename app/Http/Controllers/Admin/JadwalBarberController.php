<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\JadwalBarber;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JadwalBarberController extends Controller
{
    public function index(Request $request)
    {
        $tanggalInput = $request->input('tanggal', now()->toDateString());
        $tanggal = Carbon::parse($tanggalInput);

        $barbers = Barber::all();
        $selectedBarberId = $request->input('barber_id', $barbers->first()?->id);
        $selectedBarber = $barbers->firstWhere('id', $selectedBarberId) ?? $barbers->first();

        $jadwalList = collect();
        if ($selectedBarber) {
            $jadwalList = JadwalBarber::where('barber_id', $selectedBarber->id)
                ->whereDate('tanggal', $tanggal->toDateString())
                ->whereHas('bookings', function ($q) {
                    $q->whereIn('status', ['pending', 'checked-in', 'completed']);
                })
                ->with(['barber', 'bookings' => function ($q) {
                    $q->whereIn('status', ['pending', 'checked-in', 'completed'])->with(['layanan', 'user']);
                }])
                ->orderBy('jam_mulai')
                ->get();
        }

        // Summary status hari ini
        $totalTersedia = 0;

        $totalSibuk = JadwalBarber::whereDate('tanggal', $tanggal->toDateString())
            ->whereHas('bookings', function ($q) {
                $q->whereIn('status', ['pending', 'checked-in']);
            })
            ->when($selectedBarber, fn($q) => $q->where('barber_id', $selectedBarber->id))
            ->count();

        return view('kasir.jadwal.index', compact(
            'tanggal',
            'barbers',
            'selectedBarber',
            'jadwalList',
            'totalTersedia',
            'totalSibuk'
        ), ['title' => 'Kelola Jadwal Barber']);
    }

    // public function create()
    // {
    //     $barbers = Barber::where('status', 'masuk')->get();
    //     return view('kasir.jadwal.create', compact('barbers'), ['title' => 'Tambah Jadwal']);
    // }

    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'barber_id' => 'required|exists:barbers,id',
    //         'tanggal' => 'required|date|after_or_equal:today',
    //         'jam_mulai' => 'required|date_format:H:i',
    //         'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
    //     ]);

    //     // Cek apakah jadwal dengan barber, tanggal, dan jam yang sama sudah ada
    //     $duplikat = JadwalBarber::where('barber_id', $validated['barber_id'])
    //         ->where('tanggal', $validated['tanggal'])
    //         ->where(function ($query) use ($validated) {
    //             $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
    //                   ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
    //                   ->orWhere(function ($q) use ($validated) {
    //                       $q->where('jam_mulai', '<=', $validated['jam_mulai'])
    //                         ->where('jam_selesai', '>=', $validated['jam_selesai']);
    //                   });
    //         })
    //         ->exists();

    //     if ($duplikat) {
    //         return back()->withInput()->withErrors([
    //             'jam_mulai' => 'Jadwal barber ini sudah ada pada waktu tersebut. Pilih waktu yang berbeda.',
    //         ]);
    //     }

    //     JadwalBarber::create([
    //         ...$validated,
    //         'status' => 'tersedia',
    //     ]);

    //     return redirect()->route('kasir.jadwal.index', [
    //         'barber_id' => $validated['barber_id'],
    //         'tanggal' => $validated['tanggal']
    //     ])->with('success', 'Jadwal berhasil ditambahkan.');
    // }

    public function edit(JadwalBarber $jadwal)
    {
        $barbers = Barber::all();
        return view('kasir.jadwal.edit', compact('jadwal', 'barbers'), ['title' => 'Edit Jadwal']);
    }

    public function update(Request $request, JadwalBarber $jadwal)
    {
        $validated = $request->validate([
            'jam_mulai' => 'required|date_format:H:i',
        ]);

        $jamMulai = Carbon::parse($validated['jam_mulai'])->format('H:i');
        
        // Hitung durasi asli dari jadwal / layanan terkait
        $durasi = Carbon::parse($jadwal->jam_mulai)->diffInMinutes(Carbon::parse($jadwal->jam_selesai));
        if ($durasi <= 0) {
            $durasi = $jadwal->bookings->first()?->layanan?->durasi_menit ?? 45;
        }
        $jamSelesai = Carbon::parse($jamMulai)->addMinutes($durasi)->format('H:i');

        // Cek apakah ada jadwal lain yang bentrok untuk barber & tanggal yang sama
        $bentrok = JadwalBarber::where('barber_id', $jadwal->barber_id)
            ->whereDate('tanggal', $jadwal->tanggal->toDateString())
            ->where('id', '!=', $jadwal->id)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
            })
            ->exists();

        if ($bentrok) {
            return back()->withInput()->withErrors([
                'jam_mulai' => 'Waktu ini bentrok dengan jadwal lain untuk barber yang bersangkutan (' . $jadwal->barber->name . ').',
            ]);
        }

        $jadwal->update([
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
        ]);

        return redirect()->route('kasir.jadwal.index', [
            'barber_id' => $jadwal->barber_id,
            'tanggal' => $jadwal->tanggal->format('Y-m-d')
        ])->with('success', 'Waktu jadwal berhasil diperbarui.');
    }

    // public function destroy(JadwalBarber $jadwal)
    // {
    //     $barberId = $jadwal->barber_id;
    //     $tanggal = $jadwal->tanggal->format('Y-m-d');
    //     $jadwal->delete();

    //     return redirect()->route('kasir.jadwal.index', [
    //         'barber_id' => $barberId,
    //         'tanggal' => $tanggal
    //     ])->with('success', 'Jadwal berhasil dihapus.');
    // }
}
