<?php

namespace App\Http\Controllers;

use App\Models\DokumenProyek;
use App\Models\ProgressProyek;
use App\Models\Proyek;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalProyek' => Proyek::count(),
            'totalDokumen' => DokumenProyek::count(),
            'proyekAktif' => Proyek::query()
                ->whereNotIn('status', ['Selesai', 'Batal'])
                ->count(),
            'totalAnggota' => User::count(),
            'proyekTerbaru' => Proyek::with('klien')
                ->withMax('progressProyek', 'persentase_progress')
                ->latest()
                ->take(5)
                ->get(),
            'progressTerbaru' => ProgressProyek::with(['proyek', 'user'])
                ->latest('tanggal_laporan')
                ->take(5)
                ->get(),
        ]);
    }
}
