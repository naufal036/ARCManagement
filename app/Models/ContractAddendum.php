<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractAddendum extends Model
{
    use HasFactory;

    protected $table = 'contract_addendums';

    protected $fillable = [
        'contract_id',
        'addendum_number',
        'addendum_date',
        'additional_value',
        'description',
        'value_before',
        'value_after',
        'new_end_date',
        'file_path',
        'file_name',
    ];

    protected function casts(): array
    {
        return [
            'addendum_date' => 'date',
            'new_end_date' => 'date',
            'additional_value' => 'decimal:2',
            'value_before' => 'decimal:2',
            'value_after' => 'decimal:2',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
