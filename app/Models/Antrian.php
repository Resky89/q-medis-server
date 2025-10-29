<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Antrian extends Model
{
    use HasFactory;

    protected $fillable = [
        'loket_id',
        'nomor_antrian',
        'status',
        'waktu_panggil',
    ];

    protected $casts = [
        'waktu_panggil' => 'datetime',
    ];

    public function loket(): BelongsTo
    {
        return $this->belongsTo(Loket::class);
    }
}
