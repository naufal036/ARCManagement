<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAddendum extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'addendum_number',
        'addendum_date',
        'additional_value',
        'description',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}