<?php

namespace Database\Seeders;

use App\Models\Loket;
use Illuminate\Database\Seeder;

class LoketSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_loket' => 'Loket Pendaftaran',
                'kode_prefix' => 'A',
                'deskripsi' => 'Pendaftaran pasien',
            ],
            [
                'nama_loket' => 'Loket Pembayaran',
                'kode_prefix' => 'B',
                'deskripsi' => 'Pembayaran administrasi',
            ],
            [
                'nama_loket' => 'Loket Informasi',
                'kode_prefix' => 'C',
                'deskripsi' => 'Informasi layanan',
            ],
        ];

        foreach ($data as $item) {
            Loket::updateOrCreate(
                ['kode_prefix' => $item['kode_prefix']],
                $item
            );
        }
    }
}
