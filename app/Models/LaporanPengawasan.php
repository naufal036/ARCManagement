<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPengawasan extends Model
{
    protected $fillable = ['proyek_id', 'user_id', 'periode_mulai', 'periode_selesai', 'jenis_laporan', 'nama_pengawas', 'rencana_kegiatan', 'kegiatan_selesai', 'catatan', 'inspection_checklist', 'photo_references', 'status'];
    protected function casts(): array { return ['periode_mulai' => 'date', 'periode_selesai' => 'date', 'inspection_checklist' => 'array', 'photo_references' => 'array']; }
    public function proyek(): BelongsTo { return $this->belongsTo(Proyek::class, 'proyek_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
