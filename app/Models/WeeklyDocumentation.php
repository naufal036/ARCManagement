<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class WeeklyDocumentation extends Model
{
    protected $fillable = ['proyek_id', 'created_by', 'week_number'];
    public function proyek(): BelongsTo { return $this->belongsTo(Proyek::class, 'proyek_id'); }
    public function photos(): HasMany { return $this->hasMany(WeeklyDocumentationPhoto::class); }
    public function completeness(): int
    {
        $uploadedMandatoryPhotos = $this->photos
            ->where('is_mandatory', true)
            ->filter(fn (WeeklyDocumentationPhoto $photo): bool => $photo->path !== null && Storage::disk('private')->exists($photo->path))
            ->count();

        return (int) round(min($uploadedMandatoryPhotos, 4) / 4 * 100);
    }
}
