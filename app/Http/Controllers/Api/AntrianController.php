<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Antrian\StoreAntrianRequest;
use App\Http\Requests\Antrian\UpdateAntrianStatusRequest;
use App\Http\Resources\AntrianResource;
use App\Models\Antrian;
use App\Services\AntrianService;
use Illuminate\Http\Request;

class AntrianController extends BaseController
{
    public function __construct(private readonly AntrianService $service)
    {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage > 100) {
            $perPage = 100;
        }
        $params = $request->only(['search', 'order_by', 'order_dir', 'loket_id', 'status']);
        $antrians = $this->service->paginate($perPage, $params);

        return $this->success([
            'data' => AntrianResource::collection($antrians->items()),
            'pagination' => [
                'current_page' => $antrians->currentPage(),
                'per_page' => $antrians->perPage(),
                'total' => $antrians->total(),
                'last_page' => $antrians->lastPage(),
            ],
        ], 'antrians retrieved');
    }

    public function store(StoreAntrianRequest $request)
    {
        $antrian = $this->service->create($request->integer('loket_id'));
        return $this->success(new AntrianResource($antrian), 'antrian created', 201);
    }

    public function show(Antrian $antrian)
    {
        return $this->success(new AntrianResource($antrian), 'antrian retrieved');
    }

    public function update(UpdateAntrianStatusRequest $request, Antrian $antrian)
    {
        $status = (string) $request->string('status');
        if ($status === 'dipanggil') {
            $antrian = $this->service->call($antrian);
        } else {
            $antrian->update(['status' => $status]);
        }
        return $this->success(new AntrianResource($antrian), 'antrian updated');
    }
}
