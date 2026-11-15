<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'contract_number',
        'contract_date',
        'value',
        'description',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'project_id');
    }

    public function contractAddendums()
    {
        return $this->hasMany(ContractAddendum::class);
    }
}