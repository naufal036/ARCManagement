<?php

namespace App\Http\Controllers;

use App\Enums\DokumenVerificationStatus;
use App\Exports\DokumenArsipWorkbookExport;
use App\Models\KategoriDokumen;
use App\Services\DokumenArsipReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class LaporanArsipDokumenController extends Controller
{
    public function __construct(private readonly DokumenArsipReportService $reportService) {}

    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);
        $user = $request->user();
        $documents = $this->reportService->documents($user, $filters);
        $activities = $this->reportService->activities($user, $filters);

        return view('laporan-arsip-dokumen.index', [
            'documents' => $documents->paginate(15)->withQueryString(),
            'activities' => $activities->paginate(10, ['*'], 'activity_page')->withQueryString(),
            'summary' => $this->reportService->summary($user, $filters),
            'projects' => $this->reportService->projects($user)->get(['id', 'nama_proyek']),
            'categories' => KategoriDokumen::query()->orderBy('nama_kategori')->get(['id', 'kode_kategori', 'nama_kategori']),
            'users' => $this->reportService->uploaders($user)->get(['id', 'name']),
            'statuses' => DokumenVerificationStatus::cases(),
            'filters' => $filters,
        ]);
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        $filters = $this->validatedFilters($request);
        $user = $request->user();
        $export = new DokumenArsipWorkbookExport(
            $this->reportService->documents($user, $filters),
            $this->reportService->activities($user, $filters)
        );

        return Excel::download($export, 'laporan-arsip-dokumen-'.now()->format('Ymd-His').'.xlsx');
    }

    public function exportPdf(Request $request): Response
    {
        $filters = $this->validatedFilters($request);
        $user = $request->user();
        $documents = $this->reportService->documents($user, $filters)->get();
        $activities = $this->reportService->activities($user, $filters)->get();

        return Pdf::loadView('laporan-arsip-dokumen.pdf', [
            'documents' => $documents,
            'activities' => $activities,
            'summary' => $this->reportService->summary($user, $filters),
            'filters' => $filters,
        ])->setPaper('a4', 'landscape')
            ->download('laporan-arsip-dokumen-'.now()->format('Ymd-His').'.pdf');
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'project_id' => ['nullable', 'integer', 'exists:proyek,id'],
            'category_id' => ['nullable', 'integer', 'exists:kategori_dokumen,id'],
            'status' => ['nullable', Rule::in(DokumenVerificationStatus::values())],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'period' => ['nullable', Rule::in(['all', 'uploaded', 'revised', 'approved', 'rejected'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
    }
}
