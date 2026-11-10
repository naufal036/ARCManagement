<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proyek extends Model
{
    protected $table = 'proyek';

    protected $fillable = [
        'kode_proyek',
        'nama_proyek',
        'klien_id',
        'jenis_pekerjaan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'status',
    ];

    public function klien(): BelongsTo
    {
        return $this->belongsTo(Klien::class, 'klien_id');
    }

    public function jenisPekerjaan(): BelongsTo
    {
        return $this->belongsTo(
            JenisPekerjaan::class,
            'jenis_pekerjaan_id'
        );
    }
}
