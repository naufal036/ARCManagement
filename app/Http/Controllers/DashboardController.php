<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Klien;
use App\Models\ProgressProyek;
use App\Models\Proyek;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalProyek' => Proyek::count(),
            'totalKlien' => Klien::count(),
            'totalKontrak' => Contract::count(),
            'totalProgress' => ProgressProyek::count(),
            'proyekTerbaru' => Proyek::with('klien')
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
