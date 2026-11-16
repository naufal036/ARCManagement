<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriDokumen extends Model
{
    use HasFactory;

    protected $table = 'kategori_dokumen';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function dokumenProyek(): HasMany
    {
        return $this->hasMany(DokumenProyek::class, 'kategori_dokumen_id');
    }
}
