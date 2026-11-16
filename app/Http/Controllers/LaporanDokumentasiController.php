<?php

namespace App\Http\Controllers;

use App\Models\ProgressDokumentasi;
use App\Models\Proyek;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LaporanDokumentasiController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'project_id' => ['nullable', 'integer', 'exists:proyek,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $documentationQuery = ProgressDokumentasi::query()
            ->with(['progressProyek.proyek', 'progressProyek.user'])
            ->when($filters['project_id'] ?? null, function (Builder $query, int $projectId): void {
                $query->whereHas('progressProyek', fn (Builder $progressQuery) => $progressQuery->where('proyek_id', $projectId));
            })
            ->when($filters['date_from'] ?? null, function (Builder $query, string $date): void {
                $query->whereHas('progressProyek', fn (Builder $progressQuery) => $progressQuery->whereDate('tanggal_laporan', '>=', $date));
            })
            ->when($filters['date_to'] ?? null, function (Builder $query, string $date): void {
                $query->whereHas('progressProyek', fn (Builder $progressQuery) => $progressQuery->whereDate('tanggal_laporan', '<=', $date));
            })
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where('nama_file_asli', 'like', '%'.$search.'%');
            });

        return view('laporan-dokumentasi.index', [
            'dokumentasi' => $documentationQuery->latest()->paginate(12)->withQueryString(),
            'proyeks' => Proyek::query()->orderBy('nama_proyek')->get(['id', 'nama_proyek']),
            'filters' => $filters,
        ]);
    }
}
