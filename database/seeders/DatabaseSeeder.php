<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barber;
use App\Models\Layanan;
use App\Models\JadwalBarber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ============ USERS ============
        User::create([
            'name' => 'Admin Marko',
            'email' => 'admin@marko.com',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Owner Marko',
            'email' => 'owner@marko.com',
            'phone' => '081234567891',
            'role' => 'owner',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Budi Pelanggan',
            'email' => 'budi@email.com',
            'phone' => '081234567892',
            'role' => 'pelanggan',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Andi Pelanggan',
            'email' => 'andi@email.com',
            'phone' => '081234567893',
            'role' => 'pelanggan',
            'password' => Hash::make('password'),
        ]);

        // ============ BARBERS ============
        $barber1 = Barber::create([
            'name' => 'Rizky',
            'phone' => '081111111111',
            'status' => true,
        ]);

        $barber2 = Barber::create([
            'name' => 'Dimas',
            'phone' => '081222222222',
            'status' => true,
        ]);

        $barber3 = Barber::create([
            'name' => 'Fajar',
            'phone' => '081333333333',
            'status' => true,
        ]);

        // ============ LAYANAN ============
        Layanan::create([
            'nama_layanan' => 'Potong Rambut',
            'deskripsi' => 'Potong rambut standar pria',
            'harga' => 35000,
            'durasi_menit' => 30,
        ]);

        Layanan::create([
            'nama_layanan' => 'Potong + Cuci',
            'deskripsi' => 'Potong rambut dengan cuci rambut',
            'harga' => 50000,
            'durasi_menit' => 45,
        ]);

        Layanan::create([
            'nama_layanan' => 'Cukur Jenggot',
            'deskripsi' => 'Cukur dan rapikan jenggot',
            'harga' => 25000,
            'durasi_menit' => 20,
        ]);

        Layanan::create([
            'nama_layanan' => 'Hair Coloring',
            'deskripsi' => 'Pewarnaan rambut dengan pilihan warna',
            'harga' => 150000,
            'durasi_menit' => 90,
        ]);

        Layanan::create([
            'nama_layanan' => 'Hair Treatment',
            'deskripsi' => 'Perawatan rambut (creambath, masker rambut)',
            'harga' => 75000,
            'durasi_menit' => 60,
        ]);

        Layanan::create([
            'nama_layanan' => 'Paket Lengkap',
            'deskripsi' => 'Potong + cuci + cukur jenggot + styling',
            'harga' => 100000,
            'durasi_menit' => 75,
        ]);

        // ============ JADWAL BARBER ============
        $today = Carbon::today();

        foreach ([$barber1, $barber2, $barber3] as $barber) {
            for ($day = 0; $day < 7; $day++) {
                $date = $today->copy()->addDays($day);

                // Skip Minggu
                if ($date->isSunday()) continue;

                $slots = [
                    ['09:00', '09:30'],
                    ['09:30', '10:00'],
                    ['10:00', '10:30'],
                    ['10:30', '11:00'],
                    ['11:00', '11:30'],
                    ['13:00', '13:30'],
                    ['13:30', '14:00'],
                    ['14:00', '14:30'],
                    ['14:30', '15:00'],
                    ['15:00', '15:30'],
                    ['15:30', '16:00'],
                    ['16:00', '16:30'],
                ];

                foreach ($slots as $slot) {
                    JadwalBarber::create([
                        'barber_id' => $barber->id,
                        'tanggal' => $date->format('Y-m-d'),
                        'jam_mulai' => $slot[0],
                        'jam_selesai' => $slot[1],
                        'status' => 'tersedia',
                    ]);
                }
            }
        }
    }
}
