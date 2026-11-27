<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReport extends Model
{
    protected $fillable = ['user_id', 'proyek_id', 'title', 'status', 'priority', 'due_date', 'notes', 'is_completed'];
    protected function casts(): array { return ['due_date' => 'date', 'is_completed' => 'boolean']; }
    public function proyek(): BelongsTo { return $this->belongsTo(Proyek::class, 'proyek_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
