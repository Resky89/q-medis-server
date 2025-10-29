<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Loket\StoreLoketRequest;
use App\Http\Requests\Loket\UpdateLoketRequest;
use App\Http\Resources\LoketResource;
use App\Models\Loket;
use App\Services\LoketService;

class LoketController extends BaseController
{
    public function __construct(private readonly LoketService $service)
    {
    }

    public function index()
    {
        return $this->success(LoketResource::collection($this->service->all()));
    }

    public function store(StoreLoketRequest $request)
    {
        $loket = $this->service->create($request->validated());
        return $this->success(new LoketResource($loket), 'created', 201);
    }

    public function show(Loket $loket)
    {
        return $this->success(new LoketResource($loket));
    }

    public function update(UpdateLoketRequest $request, Loket $loket)
    {
        $loket = $this->service->update($loket, $request->validated());
        return $this->success(new LoketResource($loket));
    }

    public function destroy(Loket $loket)
    {
        $this->service->delete($loket);
        return $this->success(null, 'deleted');
    }
}
