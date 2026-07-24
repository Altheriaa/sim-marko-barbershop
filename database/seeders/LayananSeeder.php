<?php

namespace Database\Seeders;

use App\Models\Layanan;
use App\Models\SubLayanan;
use Illuminate\Database\Seeder;

class LayananSeeder extends Seeder
{
    public function run(): void
    {
        $dataLayanan = [
            [
                'nama_layanan' => 'Paket Gentleman Executive',
                'deskripsi'    => 'Potong rambut profesional, cuci rambut, cukur jenggot/kumis, hot towel, creambath & styling',
                'harga'        => 120000,
                'durasi_menit' => 60,
                'items'        => [
                    'Haircut & Custom Styling',
                    'Hair Wash & Scalp Massage',
                    'Beard Trim & Hot Towel',
                    'Creambath & Head Relaxing',
                ],
            ],
            [
                'nama_layanan' => 'Paket Cut & Wash Premium',
                'deskripsi'    => 'Potong rambut stylish, cuci rambut dengan hair tonic, pijat ringan dan styling',
                'harga'        => 75000,
                'durasi_menit' => 45,
                'items'        => [
                    'Haircut Custom Style',
                    'Hair Wash & Shampoo',
                    'Head Massage & Hair Tonic',
                    'Styling Product (Pomade/Wax)',
                ],
            ],
            [
                'nama_layanan' => 'Potong Rambut Classic',
                'deskripsi'    => 'Cukur potong rambut rapi standar pria dewasa dan anak-anak',
                'harga'        => 45000,
                'durasi_menit' => 30,
                'items'        => [
                    'Haircut Regular',
                    'Simple Styling',
                ],
            ],
            [
                'nama_layanan' => 'Cukur & Grooming Jenggot',
                'deskripsi'    => 'Pembersihan & perapihan kumis dan jenggot dengan razor presisi dan aftershave',
                'harga'        => 35000,
                'durasi_menit' => 25,
                'items'        => [
                    'Beard & Mustache Shaping',
                    'Hot Towel Treatment',
                    'Aftershave Care',
                ],
            ],
            [
                'nama_layanan' => 'Paket Hair Color & Care',
                'deskripsi'    => 'Pewarnaan rambut profesional (black/fashion color) + perawatan nutrisi rambut',
                'harga'        => 175000,
                'durasi_menit' => 90,
                'items'        => [
                    'Hair Coloring & Bleaching (Optional)',
                    'Color Lock Shampoo',
                    'Nutritive Hair Treatment',
                    'Blow Dry & Styling',
                ],
            ],
            [
                'nama_layanan' => 'Hair Spa & Creambath Therapy',
                'deskripsi'    => 'Perawatan rambut creambath dengan pijatan relaksasi leher dan pundak',
                'harga'        => 85000,
                'durasi_menit' => 50,
                'items'        => [
                    'Hair Spa Cream',
                    'Steam Hair Treatment',
                    'Shoulder & Neck Relaxing Massage',
                ],
            ],
        ];

        foreach ($dataLayanan as $item) {
            $layanan = Layanan::updateOrCreate(
                ['nama_layanan' => $item['nama_layanan']],
                [
                    'deskripsi'    => $item['deskripsi'],
                    'harga'        => $item['harga'],
                    'durasi_menit' => $item['durasi_menit'],
                ]
            );

            // Tambahkan sub-layanan (items)
            foreach ($item['items'] as $subItem) {
                SubLayanan::firstOrCreate([
                    'layanan_id' => $layanan->id,
                    'nama'       => $subItem,
                ]);
            }
        }
    }
}
