<?php

namespace App\Models;

use Database\Factories\DokumenProyekFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class DokumenProyek extends Model
{
    protected $table = 'dokumen_proyek';

    protected $fillable = [
        'proyek_id',
        'kategori_dokumen_id',
        'nama_dokumen',
        'deskripsi',
    ];

    /** @use HasFactory<DokumenProyekFactory> */
    use HasFactory;

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function kategoriDokumen(): BelongsTo
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_dokumen_id');
    }

    public function versiDokumen(): HasMany
    {
        return $this->hasMany(VersiDokumen::class, 'dokumen_proyek_id')
            ->orderByDesc('nomor_versi');
    }

    public function versiTerbaru(): HasOne
    {
        return $this->hasOne(VersiDokumen::class, 'dokumen_proyek_id')
            ->ofMany('nomor_versi', 'max');
    }
}
