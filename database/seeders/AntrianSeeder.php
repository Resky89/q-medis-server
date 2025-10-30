<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Antrian;
use App\Models\Loket;

class AntrianSeeder extends Seeder
{
    public function run(): void
    {
        $lokets = Loket::all();

        foreach ($lokets as $loket) {
            // Create 1 called (dipanggil)
            $nomor = $loket->kode_prefix . str_pad('1', (int) env('QUEUE_DIGITS', 3), '0', STR_PAD_LEFT);
            Antrian::updateOrCreate(
                ['loket_id' => $loket->id, 'nomor_antrian' => $nomor],
                [
                    'status' => 'dipanggil',
                    'waktu_panggil' => now(),
                ]
            );

            // Create menunggu entries 2..5
            for ($i = 2; $i <= 5; $i++) {
                $nomor = $loket->kode_prefix . str_pad((string) $i, (int) env('QUEUE_DIGITS', 3), '0', STR_PAD_LEFT);
                Antrian::updateOrCreate(
                    ['loket_id' => $loket->id, 'nomor_antrian' => $nomor],
                    [
                        'status' => 'menunggu',
                        'waktu_panggil' => null,
                    ]
                );
            }
        }
    }
}
