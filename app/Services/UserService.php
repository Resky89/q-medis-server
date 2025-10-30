<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserService
{
    public function __construct(private readonly UserRepositoryInterface $repo)
    {
    }

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function paginate(int $perPage = 15, array $params = []): LengthAwarePaginator
    {
        return $this->repo->paginate($perPage, $params);
    }

    public function find(int $id): ?User
    {
        return $this->repo->find($id);
    }

    public function create(array $data): User
    {
        return $this->repo->create($data);
    }

    public function update(User $user, array $data): User
    {
        return $this->repo->update($user, $data);
    }

    public function delete(User $user): void
    {
        $this->repo->delete($user);
    }
}
