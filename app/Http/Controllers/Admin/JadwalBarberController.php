<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\JadwalBarber;
use Illuminate\Http\Request;

class JadwalBarberController extends Controller
{
    public function index()
    {
        $jadwal = JadwalBarber::with('barber')->latest()->paginate(10);
        return view('admin.jadwal.index', compact('jadwal'), ['title' => 'Kelola Jadwal Barber']);
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

        JadwalBarber::create($validated);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
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

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(JadwalBarber $jadwal)
    {
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
