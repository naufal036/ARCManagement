<?php

namespace App\Exports;

use App\Models\ActivityLog;
use App\Models\DokumenProyek;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DokumenArsipWorkbookExport implements Export, WithMultipleSheets
{
    /**
     * @param  Builder<DokumenProyek>  $documents
     * @param  Builder<ActivityLog>  $activities
     */
    public function __construct(
        private readonly Builder $documents,
        private readonly Builder $activities
    ) {}

    public function sheets(): array
    {
        return [
            new DokumenArsipExport($this->documents),
            new AktivitasDokumenExport($this->activities),
        ];
    }
}
