<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $fillable = [
        'config_key',
        'config_value',
        'description',
        'updated_by',
    ];

    // Relasi untuk mengetahui Admin mana yang terakhir mengubah konfigurasi
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
