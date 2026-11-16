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
        'contract_name',
        'contract_date',
        'start_date',
        'end_date',
        'contract_value',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'contract_date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date',
            'contract_value' => 'decimal:2',
        ];
    }

    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'project_id');
    }

    public function contractAddendums()
    {
        return $this->hasMany(ContractAddendum::class);
    }
}
