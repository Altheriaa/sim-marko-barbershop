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
            'email' => 'admin@gmail.com',
            'phone' => '081234567890',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        User::create([
            'name' => 'Owner Marko',
            'email' => 'owner@gmail.com',
            'phone' => '081234567891',
            'role' => 'owner',
            'password' => Hash::make('password'),
        ]);

        // ============ BARBERS ============
        Barber::create([
            'name' => 'Rizky',
            'phone' => '081111111111',
            'status' => true,
        ]);

        Barber::create([
            'name' => 'Dimas',
            'phone' => '081222222222',
            'status' => true,
        ]);

        Barber::create([
            'name' => 'Fajar',
            'phone' => '081333333333',
            'status' => true,
        ]);
    }
}
