<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\AntrianRepositoryInterface;
use App\Models\Antrian;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AntrianRepository implements AntrianRepositoryInterface
{
    public function all(): Collection
    {
        return Antrian::query()->orderBy('id')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Antrian::query()->orderBy('id')->paginate($perPage);
    }

    public function find(int $id): ?Antrian
    {
        return Antrian::find($id);
    }

    public function create(array $data): Antrian
    {
        return Antrian::create($data);
    }

    public function update(Antrian $antrian, array $data): Antrian
    {
        $antrian->update($data);
        return $antrian;
    }
}
