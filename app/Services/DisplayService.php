<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\Loket;
use Illuminate\Support\Collection;

class DisplayService
{
    public function lokets(): Collection
    {
        return Loket::query()->orderBy('id')->get();
    }

    public function now(Loket $loket): array
    {
        $current = Antrian::query()
            ->where('loket_id', $loket->id)
            ->where('status', 'dipanggil')
            ->orderByDesc('waktu_panggil')
            ->first();

        $next = Antrian::query()
            ->where('loket_id', $loket->id)
            ->where('status', 'menunggu')
            ->orderBy('id')
            ->first();

        return [
            'loket' => $loket,
            'current' => $current,
            'next' => $next,
        ];
    }
}
