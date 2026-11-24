<?php

namespace Database\Seeders;

use App\Models\JenisPekerjaan;
use Illuminate\Database\Seeder;

class JenisPekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisPekerjaan = [
            [
                'nama_pekerjaan' => 'Pengembangan Aplikasi',
                'deskripsi' => 'Pengembangan dan penyempurnaan aplikasi perangkat lunak.',
            ],
            [
                'nama_pekerjaan' => 'Pembangunan Gedung',
                'deskripsi' => 'Pekerjaan pembangunan gedung baru.',
            ],
            [
                'nama_pekerjaan' => 'Renovasi Bangunan',
                'deskripsi' => 'Pekerjaan renovasi dan perbaikan bangunan.',
            ],
            [
                'nama_pekerjaan' => 'Instalasi Listrik',
                'deskripsi' => 'Pekerjaan pemasangan dan penataan instalasi listrik.',
            ],
            [
                'nama_pekerjaan' => 'Pemeliharaan Bangunan',
                'deskripsi' => 'Pekerjaan pemeliharaan dan perawatan bangunan.',
            ],
        ];

        foreach ($jenisPekerjaan as $jenis) {
            JenisPekerjaan::firstOrCreate(
                ['nama_pekerjaan' => $jenis['nama_pekerjaan']],
                ['deskripsi' => $jenis['deskripsi']],
            );
        }
    }
}
