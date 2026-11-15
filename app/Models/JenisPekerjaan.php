<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPekerjaan extends Model
{
    protected $table = 'jenis_pekerjaan';

    protected $fillable = [
        'nama_pekerjaan',
        'deskripsi',
    ];

    public function proyek(): HasMany
    {
        return $this->hasMany(Proyek::class, 'jenis_pekerjaan_id');
    }
}
