<?php

namespace App\Models;

use Database\Factories\JenisPekerjaanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPekerjaan extends Model
{
    /** @use HasFactory<JenisPekerjaanFactory> */
    use HasFactory;

    protected $table = 'jenis_pekerjaan';

    protected $fillable = [
        'nama_pekerjaan',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function proyek(): HasMany
    {
        return $this->hasMany(Proyek::class, 'jenis_pekerjaan_id');
    }
}
