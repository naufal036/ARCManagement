<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Role extends Model
{
    // Daftarkan kolom yang boleh diisi
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

    public function setNamaRoleAttribute(string $value): void
    {
        $this->attributes[$this->roleNameColumn()] = $value;
    }

    private function roleNameColumn(): string
    {
        return Schema::hasColumn($this->getTable(), 'name') ? 'name' : 'nama_role';
    }

    // Opsional: tambahkan relasi balik ke User jika nanti diperlukan
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
