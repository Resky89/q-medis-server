<?php

namespace App\Services;

use App\Interfaces\LoketRepositoryInterface;
use App\Models\Loket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class LoketService
{
    public function __construct(private readonly LoketRepositoryInterface $repo)
    {
    }

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage);
    }

    public function find(int $id): ?Loket
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Loket
    {
        return $this->repo->create($data);
    }

    public function update(Loket $loket, array $data): Loket
    {
        return $this->repo->update($loket, $data);
    }

    public function delete(Loket $loket): void
    {
        $this->repo->delete($loket);
    }
}
