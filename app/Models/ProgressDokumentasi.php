<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressDokumentasi extends Model
{
    protected $table = 'progress_dokumentasi';

    protected $fillable = [
        'progress_proyek_id',
        'nama_file_asli',
        'path_file',
        'keterangan',
    ];

    public function progressProyek(): BelongsTo
    {
        return $this->belongsTo(
            ProgressProyek::class,
            'progress_proyek_id'
        );
    }
}