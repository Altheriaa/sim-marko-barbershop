<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    public function index()
    {
        $layanan = Layanan::with(['subLayanan'])->withCount('subLayanan')->latest()->paginate(10);
        return view('admin.layanan.index', compact('layanan'), ['title' => 'Kelola Layanan']);
    }

    public function create()
    {
        return view('admin.layanan.create', ['title' => 'Tambah Layanan']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'durasi_menit' => 'required|integer|min:1',
            'sub_layanan' => 'nullable|array',
            'sub_layanan.*' => 'required|string|max:150',
        ]);

        $layanan = Layanan::create([
            'nama_layanan' => $validated['nama_layanan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga' => $validated['harga'],
            'durasi_menit' => $validated['durasi_menit'],
        ]);

        if (!empty($validated['sub_layanan'])) {
            foreach ($validated['sub_layanan'] as $sub) {
                $layanan->subLayanan()->create(['nama' => $sub]);
            }
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Layanan $layanan)
    {
        $layanan->load('subLayanan');
        return view('admin.layanan.edit', compact('layanan'), ['title' => 'Edit Layanan']);
    }

    public function update(Request $request, Layanan $layanan)
    {
        $validated = $request->validate([
            'nama_layanan' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'durasi_menit' => 'required|integer|min:1',
            'sub_layanan' => 'nullable|array',
            'sub_layanan.*' => 'required|string|max:150',
        ]);

        $layanan->update([
            'nama_layanan' => $validated['nama_layanan'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'harga' => $validated['harga'],
            'durasi_menit' => $validated['durasi_menit'],
        ]);

        $layanan->subLayanan()->delete(); // Hapus yang lama, insert yang baru agar mudah

        if (!empty($validated['sub_layanan'])) {
            foreach ($validated['sub_layanan'] as $sub) {
                $layanan->subLayanan()->create(['nama' => $sub]);
            }
        }

        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Layanan $layanan)
    {
        if ($layanan->bookings()->exists()) {
            return redirect()->route('admin.layanan.index')->with('error', 'Layanan tidak dapat dihapus karena sedang digunakan dalam data transaksi.');
        }

        $layanan->delete();
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }
}
