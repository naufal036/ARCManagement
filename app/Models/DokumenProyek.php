<?php

namespace App\Models;

use App\Enums\DokumenVerificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class DokumenProyek extends Model
{
    use HasFactory;

    protected $table = 'dokumen_proyek';

    protected $fillable = [
        'nomor_dokumen',
        'proyek_id',
        'kategori_dokumen_id',
        'progress_proyek_id',
        'nama_dokumen',
        'deskripsi',
        'status_verifikasi',
        'verification_cycle',
        'lock_version',
    ];

    protected $attributes = [
        'status_verifikasi' => DokumenVerificationStatus::Draft->value,
        'verification_cycle' => 0,
        'lock_version' => 0,
    ];

    protected static function booted(): void
    {
        static::creating(function (self $document): void {
            $document->nomor_dokumen ??= 'DOC-'.Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'status_verifikasi' => DokumenVerificationStatus::class,
            'verification_cycle' => 'integer',
            'lock_version' => 'integer',
        ];
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }

    public function kategoriDokumen(): BelongsTo
    {
        return $this->belongsTo(KategoriDokumen::class, 'kategori_dokumen_id');
    }

    public function progressProyek(): BelongsTo
    {
        return $this->belongsTo(ProgressProyek::class, 'progress_proyek_id');
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

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class, 'subject_id')
            ->where('subject_type', class_basename(self::class))
            ->latest('created_at')
            ->latest('id');
    }
}
