<?php

namespace App\Http\Controllers\Api;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseController
{
    public function __construct(private readonly DashboardService $service)
    {
    }

    public function index()
    {
        $user = auth('api')->user();

        if ($user->role === 'admin') {
            $data = $this->service->getAdminDashboard();
            return $this->success($data, 'Admin dashboard data retrieved');
        }

        if ($user->role === 'petugas') {
            $data = $this->service->getPetugasDashboard();
            return $this->success($data, 'Petugas dashboard data retrieved');
        }

        return $this->error('Unauthorized role', 403);
    }
}
