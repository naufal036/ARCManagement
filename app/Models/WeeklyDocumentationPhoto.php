<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyDocumentationPhoto extends Model
{
    protected $fillable = ['weekly_documentation_id', 'slot_index', 'description', 'path', 'is_mandatory'];
    protected function casts(): array { return ['is_mandatory' => 'boolean']; }
    public function weeklyDocumentation(): BelongsTo { return $this->belongsTo(WeeklyDocumentation::class); }
}
