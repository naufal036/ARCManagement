<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistItem extends Model
{
    protected $fillable = ['proyek_id', 'user_id', 'nama_kegiatan', 'kategori', 'level_urgent', 'deadline', 'catatan', 'is_completed'];

    protected function casts(): array { return ['deadline' => 'date', 'is_completed' => 'boolean']; }
    public function proyek(): BelongsTo { return $this->belongsTo(Proyek::class, 'proyek_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
