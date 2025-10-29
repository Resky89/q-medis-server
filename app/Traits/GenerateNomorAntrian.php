<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait GenerateNomorAntrian
{
    protected function formatNomor(string $prefix, int $number, int $digits = null): string
    {
        $digits = $digits ?? (int) env('QUEUE_DIGITS', 3);
        return $prefix . str_pad((string) $number, $digits, '0', STR_PAD_LEFT);
    }
}
