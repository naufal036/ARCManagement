<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        string $activity,
        ?string $description = null,
        ?Model $subject = null,
        ?int $userId = null,
        array $properties = []
    ): void {
        $actorId = $userId ?? Auth::id();

        if (! $actorId) {
            return;
        }

        static::create([
            'user_id' => $actorId,
            'activity' => $activity,
            'description' => $description,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey(),
            'ip_address' => request()->ip(),
            'properties' => $properties === [] ? null : $properties,
        ]);
    }
}
