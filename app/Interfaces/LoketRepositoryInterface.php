<?php

namespace App\Interfaces;

use App\Models\Loket;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface LoketRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15, array $params = []): LengthAwarePaginator;
    public function find(int $id): ?Loket;
    public function create(array $data): Loket;
    public function update(Loket $loket, array $data): Loket;
    public function delete(Loket $loket): void;
}
