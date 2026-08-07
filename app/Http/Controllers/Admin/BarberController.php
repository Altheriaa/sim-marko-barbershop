<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = Barber::latest()->paginate(10);
        return view('kasir.barber.index', compact('barbers'), ['title' => 'Kelola Barber']);
    }

    public function create()
    {
        return view('kasir.barber.create', ['title' => 'Tambah Barber']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:15',
            'photo'  => 'nullable|image|max:2048',
            'status' => 'required|in:masuk,cuti,nonaktif',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('barbers', 'public');
        }

        Barber::create($validated);

        return redirect()->route('kasir.barbers.index')->with('success', 'Barber berhasil ditambahkan.');
    }

    public function edit(Barber $barber)
    {
        return view('kasir.barber.edit', compact('barber'), ['title' => 'Edit Barber']);
    }

    public function update(Request $request, Barber $barber)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:15',
            'photo'  => 'nullable|image|max:2048',
            'status' => 'required|in:masuk,cuti,nonaktif',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('barbers', 'public');
        }

        $barber->update($validated);

        return redirect()->route('kasir.barbers.index')->with('success', 'Barber berhasil diperbarui.');
    }

    public function destroy(Barber $barber)
    {
        if ($barber->bookings()->exists()) {
            return redirect()->route('kasir.barbers.index')->with('error', 'Barber tidak bisa dihapus karena memiliki riwayat booking. Silakan ubah status barber sebagai gantinya.');
        }

        // Hapus jadwal terkait jika ada
        $barber->jadwal()->delete();
        
        $barber->delete();
        return redirect()->route('kasir.barbers.index')->with('success', 'Barber berhasil dihapus.');
    }

    public function toggleStatus(Request $request, Barber $barber)
    {
        if ($request->has('status') && in_array($request->status, ['masuk', 'cuti', 'nonaktif'])) {
            $newStatus = $request->status;
        } else {
            $newStatus = match ($barber->status) {
                'masuk'    => 'cuti',
                'cuti'     => 'nonaktif',
                default    => 'masuk',
            };
        }

        $barber->update(['status' => $newStatus]);
        return back()->with('success', 'Status barber ' . $barber->name . ' berhasil diubah menjadi ' . ucfirst($newStatus) . '.');
    }
}
