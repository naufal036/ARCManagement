<?php

namespace App\Models;

use Database\Factories\ProyekFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyek extends Model
{
    protected $table = 'proyek';

    /** @use HasFactory<ProyekFactory> */
    use HasFactory;

    protected $fillable = [
        'kode_proyek',
        'nama_proyek',
        'klien_id',
        'jenis_pekerjaan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'anggaran',
        'deskripsi',
        'cover_image_path',
        'status',
        'nama_kegiatan', 'instansi', 'lokasi', 'waktu_pengerjaan', 'nama_kontrak', 'nilai_kontrak', 'project_type', 'drive_url',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'anggaran' => 'decimal:2',
            'nilai_kontrak' => 'decimal:2',
            'waktu_pengerjaan' => 'integer',
        ];
    }

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

    public function progressProyek(): HasMany
    {
        return $this->hasMany(ProgressProyek::class, 'proyek_id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'project_id');
    }

    public function projectAssignments(): HasMany
    {
        return $this->hasMany(ProjectAssignment::class, 'project_id');
    }

    public function dokumenProyek(): HasMany
    {
        return $this->hasMany(DokumenProyek::class, 'proyek_id');
    }

    public function checklistItems(): HasMany { return $this->hasMany(ChecklistItem::class, 'proyek_id'); }
    public function weeklyDocumentations(): HasMany { return $this->hasMany(WeeklyDocumentation::class, 'proyek_id'); }
    public function userReports(): HasMany { return $this->hasMany(UserReport::class, 'proyek_id'); }
    public function laporanPengawasan(): HasMany { return $this->hasMany(LaporanPengawasan::class, 'proyek_id'); }
}
