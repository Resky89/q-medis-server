<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\Loket;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Traits\GenerateNomorAntrian;
use App\Support\QueryFilters;

class AntrianService
{
    use GenerateNomorAntrian;

    public function paginate(int $perPage = 15, array $params = []): LengthAwarePaginator
    {
        $query = Antrian::query();
        QueryFilters::apply(
            $query,
            $params,
            searchable: ['nomor_antrian', 'status'],
            orderable: ['id', 'loket_id', 'nomor_antrian', 'status', 'created_at', 'updated_at', 'waktu_panggil'],
            defaultOrderBy: 'id',
            defaultOrderDir: 'desc'
        );
        return $query->paginate($perPage);
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
        return DB::transaction(function () use ($antrian) {
            Antrian::query()
                ->where('loket_id', $antrian->loket_id)
                ->where('status', 'dipanggil')
                ->where('id', '!=', $antrian->id)
                ->update(['status' => 'selesai']);

            $antrian->update([
                'status' => 'dipanggil',
                'waktu_panggil' => now(),
            ]);

            return $antrian->refresh();
        });
    }
}
