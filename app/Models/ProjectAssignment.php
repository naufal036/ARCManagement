<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectAssignment extends Model
{
    use HasFactory;

    protected $table = 'project_assignments';

    protected $fillable = [
        'project_id',
        'user_id',
        'role_in_project',
        'assigned_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_date' => 'date',
        ];
    }

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projectTasks(): HasMany
    {
        return $this->hasMany(ProjectTask::class);
    }
}
