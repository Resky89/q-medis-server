<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\LoketRepositoryInterface;
use App\Models\Loket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LoketRepository implements LoketRepositoryInterface
{
    public function all(): Collection
    {
        return Loket::query()->orderBy('id')->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Loket::query()->orderBy('id')->paginate($perPage);
    }

    public function find(int $id): ?Loket
    {
        return Loket::find($id);
    }

    public function create(array $data): Loket
    {
        return Loket::create($data);
    }

    public function update(Loket $loket, array $data): Loket
    {
        $loket->update($data);
        return $loket;
    }

    public function delete(Loket $loket): void
    {
        $loket->delete();
    }
}
