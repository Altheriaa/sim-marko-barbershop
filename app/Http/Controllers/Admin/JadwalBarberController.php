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

        $barbers = Barber::where('status', true)->get();
        $selectedBarberId = $request->input('barber_id', $barbers->first()?->id);
        $selectedBarber = $barbers->firstWhere('id', $selectedBarberId) ?? $barbers->first();

        $jadwalList = collect();
        if ($selectedBarber) {
            $jadwalList = JadwalBarber::with(['barber', 'bookings.layanan', 'bookings.user'])
                ->where('barber_id', $selectedBarber->id)
                ->whereDate('tanggal', $tanggal->toDateString())
                ->orderBy('jam_mulai')
                ->get();
        }

        // Summary status hari ini untuk barber terpilih (atau keseluruhan)
        $totalTersedia = JadwalBarber::whereDate('tanggal', $tanggal->toDateString())
            ->where('status', 'tersedia')
            ->when($selectedBarber, fn($q) => $q->where('barber_id', $selectedBarber->id))
            ->count();

        $totalSibuk = JadwalBarber::whereDate('tanggal', $tanggal->toDateString())
            ->where('status', 'penuh')
            ->when($selectedBarber, fn($q) => $q->where('barber_id', $selectedBarber->id))
            ->count();

        return view('admin.jadwal.index', compact(
            'tanggal',
            'barbers',
            'selectedBarber',
            'jadwalList',
            'totalTersedia',
            'totalSibuk'
        ), ['title' => 'Kelola Jadwal Barber']);
    }

    public function create()
    {
        $barbers = Barber::where('status', true)->get();
        return view('admin.jadwal.create', compact('barbers'), ['title' => 'Tambah Jadwal']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        // Cek apakah jadwal dengan barber, tanggal, dan jam yang sama sudah ada
        $duplikat = JadwalBarber::where('barber_id', $validated['barber_id'])
            ->where('tanggal', $validated['tanggal'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                      ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                            ->where('jam_selesai', '>=', $validated['jam_selesai']);
                      });
            })
            ->exists();

        if ($duplikat) {
            return back()->withInput()->withErrors([
                'jam_mulai' => 'Jadwal barber ini sudah ada pada waktu tersebut. Pilih waktu yang berbeda.',
            ]);
        }

        JadwalBarber::create([
            ...$validated,
            'status' => 'tersedia',
        ]);

        return redirect()->route('admin.jadwal.index', [
            'barber_id' => $validated['barber_id'],
            'tanggal' => $validated['tanggal']
        ])->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(JadwalBarber $jadwal)
    {
        $barbers = Barber::where('status', true)->get();
        return view('admin.jadwal.edit', compact('jadwal', 'barbers'), ['title' => 'Edit Jadwal']);
    }

    public function update(Request $request, JadwalBarber $jadwal)
    {
        $validated = $request->validate([
            'barber_id' => 'required|exists:barbers,id',
            'tanggal' => 'required|date',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'status' => 'in:tersedia,penuh',
        ]);

        $jadwal->update($validated);

        return redirect()->route('admin.jadwal.index', [
            'barber_id' => $validated['barber_id'],
            'tanggal' => $validated['tanggal']
        ])->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(JadwalBarber $jadwal)
    {
        $barberId = $jadwal->barber_id;
        $tanggal = $jadwal->tanggal->format('Y-m-d');
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index', [
            'barber_id' => $barberId,
            'tanggal' => $tanggal
        ])->with('success', 'Jadwal berhasil dihapus.');
    }
}
