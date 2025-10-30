<?php

namespace App\Repositories\Eloquent;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Support\QueryFilters;

class UserRepository implements UserRepositoryInterface
{
    public function all(): Collection
    {
        return User::query()->orderBy('id')->get();
    }

    public function paginate(int $perPage = 15, array $params = []): LengthAwarePaginator
    {
        $query = User::query();
        QueryFilters::apply(
            $query,
            $params,
            searchable: ['name', 'email', 'role'],
            orderable: ['id', 'name', 'email', 'role', 'created_at', 'updated_at'],
            defaultOrderBy: 'id',
            defaultOrderDir: 'asc'
        );
        return $query->paginate($perPage);
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);
        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
