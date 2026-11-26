<?php

namespace App\Exports;

use App\Models\ActivityLog;
use App\Support\SpreadsheetCell;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class AktivitasDokumenExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    /**
     * @param  Builder<ActivityLog>  $activities
     */
    public function __construct(private readonly Builder $activities) {}

    /**
     * @return Builder<ActivityLog>
     */
    public function query(): Builder
    {
        return (clone $this->activities)
            ->orderBy('activity_logs.created_at')
            ->orderBy('activity_logs.id');
    }

    public function headings(): array
    {
        return ['Waktu', 'Pengguna', 'Aktivitas', 'Deskripsi', 'Status sebelum', 'Status sesudah'];
    }

    public function map(mixed $row): array
    {
        $activity = $row;
        $properties = $activity->properties ?? [];

        return [
            $activity->created_at?->format('Y-m-d H:i:s'),
            SpreadsheetCell::safeText($activity->user?->name),
            SpreadsheetCell::safeText($activity->activity),
            SpreadsheetCell::safeText($activity->description),
            SpreadsheetCell::safeText($properties['before']['status_verifikasi'] ?? null),
            SpreadsheetCell::safeText($properties['after']['status_verifikasi'] ?? null),
        ];
    }

    public function title(): string
    {
        return 'Aktivitas Dokumen';
    }
}
