<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Antrian\StoreAntrianRequest;
use App\Http\Requests\Antrian\UpdateAntrianStatusRequest;
use App\Http\Resources\AntrianResource;
use App\Models\Antrian;
use App\Services\AntrianService;

class AntrianController extends BaseController
{
    public function __construct(private readonly AntrianService $service)
    {
    }

    public function index()
    {
        return $this->success(AntrianResource::collection(Antrian::query()->orderByDesc('id')->paginate()));
    }

    public function store(StoreAntrianRequest $request)
    {
        $antrian = $this->service->create($request->integer('loket_id'));
        return $this->success(new AntrianResource($antrian), 'created', 201);
    }

    public function show(Antrian $antrian)
    {
        return $this->success(new AntrianResource($antrian));
    }

    public function update(UpdateAntrianStatusRequest $request, Antrian $antrian)
    {
        $status = $request->string('status');
        if ($status === 'dipanggil') {
            $antrian = $this->service->call($antrian);
        } else {
            $antrian->update(['status' => $status]);
        }
        return $this->success(new AntrianResource($antrian));
    }
}
