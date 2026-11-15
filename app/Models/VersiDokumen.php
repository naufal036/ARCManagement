<?php

namespace App\Models;

use Database\Factories\VersiDokumenFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersiDokumen extends Model
{
    protected $table = 'versi_dokumen';

    protected $fillable = [
        'dokumen_proyek_id',
        'nomor_versi',
        'nama_file',
        'path_file',
        'mime_type',
        'ukuran_file',
        'catatan',
        'user_id',
    ];

    /** @use HasFactory<VersiDokumenFactory> */
    use HasFactory;

    public function dokumenProyek(): BelongsTo
    {
        return $this->belongsTo(DokumenProyek::class, 'dokumen_proyek_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
