<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AntrianResource;
use App\Http\Resources\LoketResource;
use App\Models\Loket;
use App\Services\DisplayService;

class DisplayController extends BaseController
{
    public function __construct(private readonly DisplayService $service)
    {
    }

    public function lokets()
    {
        return $this->success(LoketResource::collection($this->service->lokets()), 'lokets retrieved');
    }

    public function show(Loket $loket)
    {
        $data = $this->service->now($loket);
        return $this->success([
            'loket' => new LoketResource($data['loket']),
            'current' => $data['current'] ? new AntrianResource($data['current']) : null,
            'next' => AntrianResource::collection($data['next']),
        ], 'display retrieved');
    }

    public function overview()
    {
        $items = $this->service->overview();
        $result = [];
        foreach ($items as $data) {
            $result[] = [
                'loket' => new LoketResource($data['loket']),
                'current' => $data['current'] ? new AntrianResource($data['current']) : null,
                'next' => AntrianResource::collection($data['next']),
            ];
        }
        return $this->success($result, 'overview retrieved');
    }
}
