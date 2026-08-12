<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role   = $request->input('role');

        $query = User::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        if ($role && in_array($role, ['kasir', 'admin', 'owner', 'pelanggan'])) {
            $query->where('role', $role);
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        $totalUsers     = User::count();
        $totalKasir     = User::whereIn('role', ['kasir', 'admin'])->count();
        $totalAdmin     = $totalKasir;
        $totalOwner     = User::where('role', 'owner')->count();
        $totalPelanggan = User::where('role', 'pelanggan')->count();

        return view('kasir.users.index', compact(
            'users', 'totalUsers', 'totalKasir', 'totalAdmin', 'totalOwner', 'totalPelanggan', 'search', 'role'
        ), ['title' => 'Kelola User / Pengguna']);
    }

    public function create()
    {
        $hasKasir = User::whereIn('role', ['kasir', 'admin'])->exists();
        $hasAdmin = $hasKasir;
        $hasOwner = User::where('role', 'owner')->exists();

        return view('kasir.users.create', compact('hasKasir', 'hasAdmin', 'hasOwner'), ['title' => 'Tambah User Baru']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:kasir,admin,owner,pelanggan',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (in_array($validated['role'], ['kasir', 'admin']) && User::whereIn('role', ['kasir', 'admin'])->exists()) {
            return back()->withInput()->withErrors(['role' => 'Role Kasir hanya diperbolehkan 1 akun saja dan akun Kasir sudah ada.']);
        }

        if ($validated['role'] === 'owner' && User::where('role', 'owner')->exists()) {
            return back()->withInput()->withErrors(['role' => 'Role Owner hanya diperbolehkan 1 akun saja dan akun Owner sudah ada.']);
        }

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('kasir.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $hasKasir = User::whereIn('role', ['kasir', 'admin'])->where('id', '!=', $user->id)->exists();
        $hasAdmin = $hasKasir;
        $hasOwner = User::where('role', 'owner')->where('id', '!=', $user->id)->exists();

        return view('kasir.users.edit', compact('user', 'hasKasir', 'hasAdmin', 'hasOwner'), ['title' => 'Edit User']);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:kasir,admin,owner,pelanggan',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        // Jika pengguna yang login bukan Owner (misal: Kasir), kunci role agar tetap memakai role asli user yang diedit
        $currentUser = auth()->user();
        $targetRole  = $validated['role'];

        if ($currentUser->role !== 'owner') {
            $targetRole = $user->role; // Paksa role tidak berubah
        }

        if ($targetRole !== $user->role) {
            if (in_array($targetRole, ['kasir', 'admin']) && User::whereIn('role', ['kasir', 'admin'])->where('id', '!=', $user->id)->exists()) {
                return back()->withInput()->withErrors(['role' => 'Role Kasir hanya diperbolehkan 1 akun saja.']);
            }

            if ($targetRole === 'owner' && User::where('role', 'owner')->where('id', '!=', $user->id)->exists()) {
                return back()->withInput()->withErrors(['role' => 'Role Owner hanya diperbolehkan 1 akun saja.']);
            }
        }

        $userData = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role'  => $targetRole,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $user->update($userData);

        return redirect()->route('kasir.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('kasir.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('kasir.users.index')->with('success', 'User berhasil dihapus.');
    }
}
