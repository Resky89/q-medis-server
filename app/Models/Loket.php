<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_loket',
        'kode_prefix',
        'deskripsi',
    ];

    public function antrians(): HasMany
    {
        return $this->hasMany(Antrian::class);
    }

    public function petugasLokets(): HasMany
    {
        return $this->hasMany(PetugasLoket::class);
    }
}
