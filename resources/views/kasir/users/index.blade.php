@extends('layouts.app')
@section('content')
    <x-common.page-breadcrumb :pageTitle="'Kelola User / Pengguna'" />

    {{-- Top Metric Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4 mb-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total User</span>
                    <h4 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white/90">{{ $totalUsers }}</h4>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Kasir</span>
                    <h4 class="mt-1 text-2xl font-bold text-brand-600 dark:text-brand-400">{{ $totalKasir }}</h4>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 text-purple-600 dark:bg-purple-500/10">
                    <i class="fa-solid fa-user-shield text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Owner</span>
                    <h4 class="mt-1 text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $totalOwner }}</h4>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-500/10">
                    <i class="fa-solid fa-user-tie text-lg"></i>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Pelanggan</span>
                    <h4 class="mt-1 text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalPelanggan }}</h4>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/10">
                    <i class="fa-solid fa-user text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar & Actions --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <form method="GET" action="{{ route('kasir.users.index') }}" class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-[260px]">
                    <span class="absolute -translate-y-1/2 pointer-events-none left-3.5 top-1/2 text-gray-400">
                        <i class="fa-solid fa-magnifying-glass text-sm"></i>
                    </span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, email, HP..."
                        class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-9 pr-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                </div>

                <select name="role" onchange="this.form.submit()"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua Role</option>
                    <option value="kasir" {{ in_array($role, ['kasir', 'admin']) ? 'selected' : '' }}>Kasir</option>
                    <option value="owner" {{ $role === 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="pelanggan" {{ $role === 'pelanggan' ? 'selected' : '' }}>Pelanggan</option>
                </select>

                <button type="submit" class="h-10 rounded-lg bg-brand-500 px-4 text-sm font-semibold text-white hover:bg-brand-600 transition">
                    Filter
                </button>
                @if($search || $role)
                    <a href="{{ route('kasir.users.index') }}" class="h-10 inline-flex items-center rounded-lg border border-gray-300 px-4 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 transition">
                        Reset
                    </a>
                @endif
            </div>

            <a href="{{ route('kasir.users.create') }}" class="h-10 inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 text-sm font-semibold text-gray-800 shadow-theme-xs hover:border-brand-500 hover:bg-brand-50 hover:text-brand-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:border-brand-500 dark:hover:bg-brand-950/40 dark:hover:text-brand-400 transition-all">
                <i class="fa-solid fa-plus text-brand-500"></i> Tambah User Baru
            </a>
        </form>
    </div>

    {{-- Users List Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/50 text-gray-600 dark:text-gray-400 font-semibold uppercase text-xs tracking-wider">
                        <th class="py-4 px-5">#</th>
                        <th class="py-4 px-5">Nama Pengguna</th>
                        <th class="py-4 px-5">Email</th>
                        <th class="py-4 px-5">No. HP</th>
                        <th class="py-4 px-5">Role</th>
                        <th class="py-4 px-5">Terdaftar</th>
                        <th class="py-4 px-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition">
                            <td class="py-4 px-5 text-sm text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 text-brand-600 font-bold text-sm uppercase dark:bg-brand-500/20 dark:text-brand-400">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-sm text-gray-900 dark:text-white block">{{ $user->name }}</span>
                                        @if(auth()->id() === $user->id)
                                            <span class="inline-block text-xs bg-brand-50 text-brand-600 px-2 py-0.5 rounded font-semibold dark:bg-brand-500/10">Akun Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 font-mono text-sm text-gray-700 dark:text-gray-300">{{ $user->email }}</td>
                            <td class="py-4 px-5 text-sm text-gray-700 dark:text-gray-300">{{ $user->phone ?? '-' }}</td>
                            <td class="py-4 px-5">
                                @if(in_array($user->role, ['kasir', 'admin']))
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-purple-100 px-3 py-1 text-xs font-bold text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                        <i class="fa-solid fa-cash-register text-xs"></i> Kasir
                                    </span>
                                @elseif($user->role === 'owner')
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                        <i class="fa-solid fa-user-tie text-xs"></i> Owner
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i class="fa-solid fa-user text-xs"></i> Pelanggan
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-sm text-gray-600 dark:text-gray-400">
                                {{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="py-4 px-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('kasir.users.edit', $user) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-brand-50 hover:text-brand-600 hover:border-brand-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition" title="Edit User">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('kasir.users.destroy', $user) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400 transition" title="Hapus User">
                                                <i class="fa-solid fa-trash-can text-sm"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada data user yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-800">
            {{ $users->links() }}
        </div>
    </div>
@endsection
