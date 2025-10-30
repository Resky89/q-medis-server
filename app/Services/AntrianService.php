<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\Loket;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Traits\GenerateNomorAntrian;

class AntrianService
{
    use GenerateNomorAntrian;

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Antrian::query()->orderByDesc('id')->paginate($perPage);
    }

    public function create(int $loketId): Antrian
    {
        $loket = Loket::findOrFail($loketId);

        $nextNumber = (int) DB::table('antrians')
            ->where('loket_id', $loketId)
            ->whereDate('created_at', now()->toDateString())
            ->count() + 1;

        $nomor = $this->formatNomor($loket->kode_prefix, $nextNumber);

        return Antrian::create([
            'loket_id' => $loketId,
            'nomor_antrian' => $nomor,
            'status' => 'menunggu',
        ]);
    }

    public function call(Antrian $antrian): Antrian
    {
        $antrian->update([
            'status' => 'dipanggil',
            'waktu_panggil' => now(),
        ]);

        return $antrian;
    }
}
