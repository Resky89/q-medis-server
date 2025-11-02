<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\Loket;
use App\Models\User;
use App\Models\PetugasLoket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getAdminDashboard(): array
    {
        $today = now()->toDateString();

        return [
            'statistics' => [
                'total_lokets' => Loket::count(),
                'total_users' => User::count(),
                'total_petugas' => User::where('role', 'petugas')->count(),
                'total_antrians_today' => Antrian::whereDate('created_at', $today)->count(),
            ],
            'antrian_by_status' => [
                'menunggu' => Antrian::whereDate('created_at', $today)
                    ->where('status', 'menunggu')
                    ->count(),
                'dipanggil' => Antrian::whereDate('created_at', $today)
                    ->where('status', 'dipanggil')
                    ->count(),
                'selesai' => Antrian::whereDate('created_at', $today)
                    ->where('status', 'selesai')
                    ->count(),
            ],
            'loket_statistics' => Loket::withCount([
                'antrians as total_today' => function ($query) use ($today) {
                    $query->whereDate('created_at', $today);
                },
                'antrians as menunggu' => function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->where('status', 'menunggu');
                },
                'antrians as dipanggil' => function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->where('status', 'dipanggil');
                },
                'antrians as selesai' => function ($query) use ($today) {
                    $query->whereDate('created_at', $today)
                        ->where('status', 'selesai');
                },
            ])->get(),
            'recent_antrians' => Antrian::with('loket')
                ->whereDate('created_at', $today)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get(),
            'hourly_statistics' => DB::table('antrians')
                ->selectRaw("EXTRACT(HOUR FROM created_at) as hour, COUNT(*) as total")
                ->whereDate('created_at', $today)
                ->groupBy('hour')
                ->orderBy('hour')
                ->get(),
        ];
    }

    public function getPetugasDashboard(): array
    {
        $user = auth('api')->user();
        $today = now()->toDateString();

        // Get lokets assigned to this petugas
        $petugasLokets = PetugasLoket::where('user_id', $user->id)
            ->with('loket')
            ->get();

        $loketIds = $petugasLokets->pluck('loket_id');

        if ($loketIds->isEmpty()) {
            return [
                'statistics' => [
                    'total_antrians_today' => Antrian::whereDate('created_at', $today)->count(),
                    'menunggu' => Antrian::whereDate('created_at', $today)
                        ->where('status', 'menunggu')
                        ->count(),
                    'dipanggil' => Antrian::whereDate('created_at', $today)
                        ->where('status', 'dipanggil')
                        ->count(),
                    'selesai' => Antrian::whereDate('created_at', $today)
                        ->where('status', 'selesai')
                        ->count(),
                ],
                'current_queue' => Antrian::with('loket')
                    ->whereDate('created_at', $today)
                    ->where('status', 'dipanggil')
                    ->first(),
                'next_queue' => Antrian::with('loket')
                    ->whereDate('created_at', $today)
                    ->where('status', 'menunggu')
                    ->orderBy('created_at', 'asc')
                    ->first(),
                'recent_antrians' => Antrian::with('loket')
                    ->whereDate('created_at', $today)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'loket_statistics' => Loket::withCount([
                    'antrians as total_today' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today);
                    },
                    'antrians as menunggu' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today)
                            ->where('status', 'menunggu');
                    },
                    'antrians as dipanggil' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today)
                            ->where('status', 'dipanggil');
                    },
                    'antrians as selesai' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today)
                            ->where('status', 'selesai');
                    },
                ])->get(),
            ];
        }

        return [
            'statistics' => [
                'total_antrians_today' => Antrian::whereIn('loket_id', $loketIds)
                    ->whereDate('created_at', $today)
                    ->count(),
                'menunggu' => Antrian::whereIn('loket_id', $loketIds)
                    ->whereDate('created_at', $today)
                    ->where('status', 'menunggu')
                    ->count(),
                'dipanggil' => Antrian::whereIn('loket_id', $loketIds)
                    ->whereDate('created_at', $today)
                    ->where('status', 'dipanggil')
                    ->count(),
                'selesai' => Antrian::whereIn('loket_id', $loketIds)
                    ->whereDate('created_at', $today)
                    ->where('status', 'selesai')
                    ->count(),
            ],
            'current_queue' => Antrian::with('loket')
                ->whereIn('loket_id', $loketIds)
                ->whereDate('created_at', $today)
                ->where('status', 'dipanggil')
                ->first(),
            'next_queue' => Antrian::with('loket')
                ->whereIn('loket_id', $loketIds)
                ->whereDate('created_at', $today)
                ->where('status', 'menunggu')
                ->orderBy('created_at', 'asc')
                ->first(),
            'recent_antrians' => Antrian::with('loket')
                ->whereIn('loket_id', $loketIds)
                ->whereDate('created_at', $today)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'loket_statistics' => Loket::whereIn('id', $loketIds)
                ->withCount([
                    'antrians as total_today' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today);
                    },
                    'antrians as menunggu' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today)
                            ->where('status', 'menunggu');
                    },
                    'antrians as dipanggil' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today)
                            ->where('status', 'dipanggil');
                    },
                    'antrians as selesai' => function ($query) use ($today) {
                        $query->whereDate('created_at', $today)
                            ->where('status', 'selesai');
                    },
                ])->get(),
        ];
    }
}
