<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\LoketRepositoryInterface;
use App\Models\Loket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Support\QueryFilters;

class LoketRepository implements LoketRepositoryInterface
{
    public function all(): Collection
    {
        return Loket::query()->orderBy('id')->get();
    }

    public function paginate(int $perPage = 15, array $params = []): LengthAwarePaginator
    {
        $query = Loket::query();
        QueryFilters::apply(
            $query,
            $params,
            searchable: ['nama_loket', 'kode_prefix', 'deskripsi'],
            orderable: ['id', 'nama_loket', 'kode_prefix', 'created_at', 'updated_at'],
            defaultOrderBy: 'id',
            defaultOrderDir: 'asc'
        );
        return $query->paginate($perPage);
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
