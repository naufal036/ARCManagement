<?php

namespace App\Exports;

use App\Models\DokumenProyek;
use App\Support\SpreadsheetCell;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class DokumenArsipExport implements FromQuery, WithHeadings, WithMapping, WithTitle
{
    /**
     * @param  Builder<DokumenProyek>  $documents
     */
    public function __construct(private readonly Builder $documents) {}

    /**
     * @return Builder<DokumenProyek>
     */
    public function query(): Builder
    {
        return (clone $this->documents)
            ->orderBy('dokumen_proyek.created_at')
            ->orderBy('dokumen_proyek.id');
    }

    public function headings(): array
    {
        return [
            'Nomor dokumen',
            'Nama dokumen',
            'Proyek',
            'Kategori',
            'Status verifikasi',
            'Versi terbaru',
            'Ekstensi',
            'Pengunggah terakhir',
            'Tanggal dibuat',
            'Tanggal revisi terakhir',
        ];
    }

    public function map(mixed $row): array
    {
        $document = $row;

        return [
            SpreadsheetCell::safeText($document->nomor_dokumen),
            SpreadsheetCell::safeText($document->nama_dokumen),
            SpreadsheetCell::safeText($document->proyek?->nama_proyek),
            SpreadsheetCell::safeText($document->kategoriDokumen?->nama_kategori),
            $document->status_verifikasi->label(),
            $document->versiTerbaru?->nomor_versi ?? '-',
            SpreadsheetCell::safeText($document->versiTerbaru?->ekstensi),
            SpreadsheetCell::safeText($document->versiTerbaru?->pengunggah?->name),
            $document->created_at?->format('Y-m-d H:i:s'),
            $document->versiTerbaru?->created_at?->format('Y-m-d H:i:s') ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Arsip Dokumen';
    }
}
