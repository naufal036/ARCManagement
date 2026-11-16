<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ProgressProyek extends Model
{
    protected $table = 'progress_proyek';

    protected $fillable = [
        'proyek_id',
        'user_id',
        'tanggal_laporan',
        'persentase_progress',
        'uraian_pekerjaan',
        'kendala',
        'solusi',
        'catatan',
        'rencana_selanjutnya',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_laporan' => 'date',
            'persentase_progress' => 'decimal:2',
        ];
    }

    // Relasi dengan model Proyek
    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function dokumentasi(): HasMany
    {
        return $this->hasMany(
            ProgressDokumentasi::class,
            'progress_proyek_id'
        );
    }
}
