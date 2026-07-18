@extends('layouts.app')
@section('content')
<x-common.page-breadcrumb :pageTitle="'Edit Jadwal'" />
<div class="max-w-2xl">
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <form action="{{ route('admin.jadwal.update', $jadwal) }}" method="POST">@csrf @method('PUT')
            <div class="space-y-5">
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Barber <span class="text-error-500">*</span></label>
                    <select name="barber_id" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach($barbers as $barber)<option value="{{ $barber->id }}" {{ old('barber_id', $jadwal->barber_id) == $barber->id ? 'selected' : '' }}>{{ $barber->name }}</option>@endforeach
                    </select></div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tanggal <span class="text-error-500">*</span></label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', $jadwal->tanggal->format('Y-m-d')) }}" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" /></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jam Mulai</label>
                        <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $jadwal->jam_mulai) }}" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" /></div>
                    <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Jam Selesai</label>
                        <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $jadwal->jam_selesai) }}" required class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" /></div>
                </div>
                <div><label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                    <select name="status" class="shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        <option value="tersedia" {{ $jadwal->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="penuh" {{ $jadwal->status === 'penuh' ? 'selected' : '' }}>Penuh</option>
                    </select></div>
                <div class="flex gap-3">
                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition">Perbarui</button>
                    <a href="{{ route('admin.jadwal.index') }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800 transition">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
