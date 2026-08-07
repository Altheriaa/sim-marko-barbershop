@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb :pageTitle="'Profil Saya'" />

    <div class="space-y-6">

        {{-- Profile Banner Card --}}
        <div
            class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03] shadow-theme-xs">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex flex-col sm:flex-row items-center gap-5 text-center sm:text-left">
                    {{-- User Avatar --}}
                    <div
                        class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-brand-500 text-white font-bold text-2xl shadow-theme-xs">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                    </div>

                    <div>
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5 mb-1">
                            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $user->name }}</h2>
                            @php
                                $roleBadge = match ($user->role) {
                                    'kasir', 'admin' => ['bg' => 'bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300 border-purple-200 dark:border-purple-800', 'label' => '💳 Kasir'],
                                    'owner' => ['bg' => 'bg-amber-50 dark:bg-amber-950/50 text-amber-700 dark:text-amber-300 border-amber-200 dark:border-amber-800', 'label' => '💼 Owner'],
                                    default => ['bg' => 'bg-brand-50 dark:bg-brand-950/50 text-brand-700 dark:text-brand-300 border-brand-200 dark:border-brand-800', 'label' => '✂️ Pelanggan'],
                                };
                            @endphp
                            <span
                                class="inline-flex items-center rounded-full px-3 py-0.5 text-xs font-bold border {{ $roleBadge['bg'] }}">
                                {{ $roleBadge['label'] }}
                            </span>
                        </div>
                        <p
                            class="text-xs text-gray-500 dark:text-gray-400 flex flex-wrap items-center justify-center sm:justify-start gap-3">
                            <span><i class="fa-solid fa-envelope text-gray-400 mr-1"></i> {{ $user->email }}</span>
                            <span>&bull;</span>
                            <span><i class="fa-solid fa-phone text-gray-400 mr-1"></i>
                                {{ $user->phone ?: 'Belum ada no hp' }}</span>
                        </p>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                            <i class="fa-solid fa-calendar-check mr-1"></i> Bergabung sejak
                            {{ $user->created_at ? $user->created_at->locale('id')->translatedFormat('d F Y') : '-' }}
                        </p>
                    </div>
                </div>

                {{-- Metric Badge Summary --}}
                <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                    @if($user->role === 'pelanggan')
                        <div
                            class="flex-1 md:flex-initial rounded-xl bg-gray-50 dark:bg-gray-800/60 p-3.5 text-center border border-gray-100 dark:border-gray-800 min-w-[120px]">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Total Booking</span>
                            <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['total_booking'] }}</span>
                        </div>
                        <div
                            class="flex-1 md:flex-initial rounded-xl bg-gray-50 dark:bg-gray-800/60 p-3.5 text-center border border-gray-100 dark:border-gray-800 min-w-[120px]">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Selesai</span>
                            <span
                                class="text-lg font-bold text-emerald-600 dark:text-emerald-400">{{ $stats['booking_selesai'] }}</span>
                        </div>
                    @elseif(in_array($user->role, ['kasir', 'admin']))
                        <div
                            class="flex-1 md:flex-initial rounded-xl bg-gray-50 dark:bg-gray-800/60 p-3.5 text-center border border-gray-100 dark:border-gray-800 min-w-[120px]">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Total Booking</span>
                            <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['total_booking'] }}</span>
                        </div>
                        <div
                            class="flex-1 md:flex-initial rounded-xl bg-gray-50 dark:bg-gray-800/60 p-3.5 text-center border border-gray-100 dark:border-gray-800 min-w-[120px]">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Total Transaksi</span>
                            <span
                                class="text-lg font-bold text-brand-600 dark:text-brand-400">{{ $stats['total_transaksi'] }}</span>
                        </div>
                    @elseif($user->role === 'owner')
                        <div
                            class="flex-1 md:flex-initial rounded-xl bg-gray-50 dark:bg-gray-800/60 p-3.5 text-center border border-gray-100 dark:border-gray-800 min-w-[120px]">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Total Transaksi</span>
                            <span class="text-lg font-bold text-gray-800 dark:text-white">{{ $stats['total_transaksi'] }}</span>
                        </div>
                        <div
                            class="flex-1 md:flex-initial rounded-xl bg-gray-50 dark:bg-gray-800/60 p-3.5 text-center border border-gray-100 dark:border-gray-800 min-w-[140px]">
                            <span class="text-xs text-gray-500 dark:text-gray-400 block font-medium">Omset Pendapatan</span>
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp
                                {{ number_format($stats['total_pendapatan'], 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Form Section Grid --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

            {{-- Left Column: Form Informasi Pribadi --}}
            <div class="lg:col-span-7 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Informasi Pribadi</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Perbarui data diri dan kontak akun Anda</p>
                        </div>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-950/40 dark:text-brand-400">
                            <i class="fa-solid fa-user-pen text-sm"></i>
                        </span>
                    </div>

                    @if(session('success'))
                        <div
                            class="mb-5 rounded-xl bg-green-50 p-4 text-xs font-semibold text-green-700 dark:bg-green-950/40 dark:text-green-400 border border-green-200 dark:border-green-800/40">
                            <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Nama
                                    Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Alamat
                                    Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">No.
                                    WhatsApp / HP</label>
                                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                                    placeholder="Contoh: 081234567890"
                                    class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                @error('phone')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">Hak
                                    Akses / Role</label>
                                <input type="text" value="{{ ucfirst($user->role) }}" disabled
                                    class="h-11 w-full rounded-xl border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-500 dark:border-gray-800 dark:bg-gray-800 dark:text-gray-400 cursor-not-allowed" />
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="rounded-xl bg-brand-500 px-5 py-2.5 text-sm font-bold text-white shadow-theme-xs hover:bg-brand-600 transition">
                                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Right Column: Form Keamanan & Ganti Password --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 dark:border-gray-800 mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Keamanan Akun</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Ubah password akun Anda secara berkala</p>
                        </div>
                        <span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400">
                            <i class="fa-solid fa-key text-sm"></i>
                        </span>
                    </div>

                    @if(session('success_password'))
                        <div
                            class="mb-5 rounded-xl bg-green-50 p-4 text-xs font-semibold text-green-700 dark:bg-green-950/40 dark:text-green-400 border border-green-200 dark:border-green-800/40">
                            <i class="fa-solid fa-circle-check mr-1.5"></i> {{ session('success_password') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Password
                                    Saat Ini <span class="text-red-500">*</span></label>
                                <input type="password" name="current_password" required placeholder="Masukkan password lama"
                                    class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                @error('current_password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Password
                                    Baru <span class="text-red-500">*</span></label>
                                <input type="password" name="password" required placeholder="Minimal 8 karakter"
                                    class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                                @error('password')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label
                                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-gray-600 dark:text-gray-400">Konfirmasi
                                    Password Baru <span class="text-red-500">*</span></label>
                                <input type="password" name="password_confirmation" required
                                    placeholder="Ulangi password baru"
                                    class="h-11 w-full rounded-xl border border-gray-300 bg-transparent px-4 py-2.5 text-sm font-medium text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:border-brand-500 focus:ring-1 focus:ring-brand-500" />
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-bold text-white shadow-theme-xs hover:bg-amber-600 transition">
                                    <i class="fa-solid fa-shield-halved mr-1.5"></i> Perbarui Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

    </div>
@endsection