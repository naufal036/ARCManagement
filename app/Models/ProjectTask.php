<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_assignment_id',
        'task_name',
        'status',
        'due_date',
    ];

    public function projectAssignment()
    {
        return $this->belongsTo(ProjectAssignment::class);
    }
}
