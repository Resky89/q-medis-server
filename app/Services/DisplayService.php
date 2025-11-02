<?php

namespace App\Services;

use App\Models\Antrian;
use App\Models\Loket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DisplayService
{
    private static ?string $lastCleanupDate = null;

    public function lokets(): Collection
    {
        return Loket::query()->orderBy('id')->get();
    }

    public function now(Loket $loket): array
    {
        // Auto cleanup antrian lama sekali sehari
        $this->cleanupOldAntrian();

        $current = Antrian::query()
            ->where('loket_id', $loket->id)
            ->where('status', 'dipanggil')
            ->whereDate('created_at', now()->toDateString())
            ->orderByDesc('waktu_panggil')
            ->first();

        $next = Antrian::query()
            ->where('loket_id', $loket->id)
            ->where('status', 'menunggu')
            ->whereDate('created_at', now()->toDateString())
            ->orderBy('id')
            ->limit(2)
            ->get();

        return [
            'loket' => $loket,
            'current' => $current,
            'next' => $next,
        ];
    }

    public function overview(): array
    {
        $items = [];
        foreach ($this->lokets() as $loket) {
            $items[] = $this->now($loket);
        }
        return $items;
    }

    /**
     * Cleanup antrian dari hari sebelumnya
     * Hanya berjalan sekali per hari untuk menghindari query berlebihan
     */
    private function cleanupOldAntrian(): void
    {
        $today = now()->toDateString();

        // Skip jika sudah cleanup hari ini
        if (self::$lastCleanupDate === $today) {
            return;
        }

        try {
            // Hapus antrian yang bukan hari ini
            Antrian::query()
                ->whereDate('created_at', '<', $today)
                ->delete();

            // Simpan tanggal cleanup
            self::$lastCleanupDate = $today;
        } catch (\Throwable $e) {
            // Silent fail - jangan ganggu display jika cleanup error
            \Log::error('Failed to cleanup old antrian: ' . $e->getMessage());
        }
    }
}
