<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'description',
        'nama_role',
    ];

    public function setNamaRoleAttribute(?string $value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getNamaRoleAttribute(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
