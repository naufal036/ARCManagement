<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // Daftarkan kolom yang boleh diisi
    protected $fillable = [
        'name',
        'description',
    ];

    // Opsional: tambahkan relasi balik ke User jika nanti diperlukan
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
