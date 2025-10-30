<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PetugasLoket;
use App\Models\User;
use App\Models\Loket;

class PetugasLoketSeeder extends Seeder
{
    public function run(): void
    {
        $petugas1 = User::where('email', 'petugas1@example.com')->first();
        $petugas2 = User::where('email', 'petugas2@example.com')->first();

        $loketA = Loket::where('kode_prefix', 'A')->first();
        $loketB = Loket::where('kode_prefix', 'B')->first();

        if ($petugas1 && $loketA) {
            PetugasLoket::updateOrCreate(
                [
                    'user_id' => $petugas1->id,
                    'loket_id' => $loketA->id,
                ],
                [
                    'tanggal_mulai' => now()->toDateString(),
                    'tanggal_selesai' => null,
                ]
            );
        }

        if ($petugas2 && $loketB) {
            PetugasLoket::updateOrCreate(
                [
                    'user_id' => $petugas2->id,
                    'loket_id' => $loketB->id,
                ],
                [
                    'tanggal_mulai' => now()->toDateString(),
                    'tanggal_selesai' => null,
                ]
            );
        }
    }
}
