<?php

namespace App\Interfaces;

use App\Models\Antrian;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface AntrianRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Antrian;
    public function create(array $data): Antrian;
    public function update(Antrian $antrian, array $data): Antrian;
}
