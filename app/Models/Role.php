<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Role extends Model
{
    protected $fillable = [
        'name',
        'nama_role',
        'description',
    ];

    public function getNameAttribute(?string $value): ?string
    {
        return $value ?? ($this->attributes['nama_role'] ?? null);
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes[$this->roleNameColumn()] = $value;
    }

    private function roleNameColumn(): string
    {
        return Schema::hasColumn($this->getTable(), 'name') ? 'name' : 'nama_role';
    }

    public function getNamaRoleAttribute(): ?string
    {
        return $this->attributes['nama_role'] ?? $this->attributes['name'] ?? null;
    }

    public function setNamaRoleAttribute(string $value): void
    {
        $this->attributes[$this->roleNameColumn()] = $value;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
