<?php

namespace App\Models;

use Database\Factories\KlienFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klien extends Model
{
    /** @use HasFactory<KlienFactory> */
    use HasFactory;

    protected $table = 'klien';

    protected $fillable = [
        'nama_klien',
        'jenis_client',
        'npsn',
        'alamat',
        'no_telepon',
        'email',
    ];

    public function proyek(): HasMany
    {
        return $this->hasMany(Proyek::class, 'klien_id');
    }
}
