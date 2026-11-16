<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersiDokumen extends Model
{
    use HasFactory;

    protected $table = 'versi_dokumen';

    protected $fillable = [
        'dokumen_proyek_id',
        'nomor_versi',
        'nama_file_asli',
        'nama_file_storage',
        'path_file',
        'disk',
        'mime_type',
        'ekstensi',
        'ukuran_file',
        'checksum_sha256',
        'catatan',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'ukuran_file' => 'integer',
        ];
    }

    public function dokumenProyek(): BelongsTo
    {
        return $this->belongsTo(DokumenProyek::class, 'dokumen_proyek_id');
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
