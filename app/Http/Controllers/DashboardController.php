<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\DokumenProyek;
use App\Models\JenisPekerjaan;
use App\Models\Klien;
use App\Models\ProgressProyek;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $visibleProjects = Proyek::query()
            ->when($user->role?->name !== null && ! in_array($user->role->name, ['Admin', 'Direktur'], true), fn ($query) => $query->whereHas('projectAssignments', fn ($assignmentQuery) => $assignmentQuery->where('user_id', $user->id)));
        $projectIds = (clone $visibleProjects)->pluck('id');

        return view('dashboard', [
            'totalProyek' => $projectIds->count(),
            'totalKlien' => Klien::whereIn('id', (clone $visibleProjects)->select('klien_id'))->count(),
            'totalJenisPekerjaan' => JenisPekerjaan::whereIn('id', (clone $visibleProjects)->select('jenis_pekerjaan_id'))->count(),
            'totalKontrak' => Contract::whereIn('project_id', $projectIds)->count(),
            'totalDokumen' => DokumenProyek::whereIn('proyek_id', $projectIds)->count(),
            'totalProgress' => ProgressProyek::whereIn('proyek_id', $projectIds)->count(),
            'proyekAktif' => (clone $visibleProjects)
                ->whereNotIn('status', ['Selesai', 'Batal'])
                ->count(),
            'totalAnggota' => $user->role?->name !== null && ! in_array($user->role->name, ['Admin', 'Direktur'], true) ? 1 : User::count(),
            'jumlahProyekPerStatus' => (clone $visibleProjects)
                ->select('status')
                ->selectRaw('COUNT(*) as total')
                ->groupBy('status')
                ->orderBy('status')
                ->get(),
            'proyekTerbaru' => (clone $visibleProjects)->with('klien')
                ->withMax('progressProyek', 'persentase_progress')
                ->latest()
                ->take(5)
                ->get(),
            'progressTerbaru' => ProgressProyek::with(['proyek', 'user'])
                ->whereIn('proyek_id', $projectIds)
                ->latest('tanggal_laporan')
                ->take(5)
                ->get(),
        ]);
    }
}
