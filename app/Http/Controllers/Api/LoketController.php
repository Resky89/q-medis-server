<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Loket\StoreLoketRequest;
use App\Http\Requests\Loket\UpdateLoketRequest;
use App\Http\Resources\LoketResource;
use App\Models\Loket;
use App\Services\LoketService;
use Illuminate\Http\Request;

class LoketController extends BaseController
{
    public function __construct(private readonly LoketService $service)
    {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage > 100) {
            $perPage = 100;
        }
        $params = $request->only(['search', 'order_by', 'order_dir']);
        $lokets = $this->service->paginate($perPage, $params);

        return $this->success([
            'data' => LoketResource::collection($lokets->items()),
            'pagination' => [
                'current_page' => $lokets->currentPage(),
                'per_page' => $lokets->perPage(),
                'total' => $lokets->total(),
                'last_page' => $lokets->lastPage(),
            ],
        ], 'lokets retrieved');
    }

    public function store(StoreLoketRequest $request)
    {
        $loket = $this->service->create($request->validated());
        return $this->success(new LoketResource($loket), 'loket created', 201);
    }

    public function show(Loket $loket)
    {
        return $this->success(new LoketResource($loket), 'loket retrieved');
    }

    public function update(UpdateLoketRequest $request, Loket $loket)
    {
        $loket = $this->service->update($loket, $request->validated());
        return $this->success(new LoketResource($loket), 'loket updated');
    }

    public function destroy(Loket $loket)
    {
        $this->service->delete($loket);
        return $this->success(null, 'loket deleted');
    }
}
