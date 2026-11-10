<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Klien extends Model
{
    protected $table = 'klien';

    protected $fillable = [
        'nama_klien',
        'alamat',
        'no_telepon',
        'email',
    ];

    public function proyek(): HasMany
    {
        return $this->hasMany(Proyek::class, 'klien_id');
    }
}
