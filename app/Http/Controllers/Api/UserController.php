<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Schema;

class UserController extends BaseController
{
    public function __construct(private readonly UserService $service)
    {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage > 100) {
            $perPage = 100;
        }
        $users = $this->service->paginate($perPage);

        return $this->success([
            'data' => UserResource::collection($users->items()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
            ],
        ], 'users retrieved');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        if (! Schema::hasColumn('users', 'role')) {
            unset($data['role']);
        } elseif (! isset($data['role'])) {
            $data['role'] = 'petugas';
        }

        $user = $this->service->create($data);

        return $this->success(new UserResource($user), 'user created', 201);
    }

    public function show(User $user)
    {
        return $this->success(new UserResource($user), 'user retrieved');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (! Schema::hasColumn('users', 'role')) {
            unset($data['role']);
        }

        $user = $this->service->update($user, $data);

        return $this->success(new UserResource($user), 'user updated');
    }

    public function destroy(User $user)
    {
        $this->service->delete($user);

        return $this->success(null, 'user deleted', 200);
    }
}

