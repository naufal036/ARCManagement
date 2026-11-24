
<?php

use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenProyekController;
use App\Http\Controllers\JenisPekerjaanController;
use App\Http\Controllers\KategoriDokumenController;
use App\Http\Controllers\KlienController;
use App\Http\Controllers\LaporanDokumentasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgressProyekController;
use App\Http\Controllers\ProjectAssignmentController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\VersiDokumenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('klien', KlienController::class);
    Route::resource('jenis-pekerjaan', JenisPekerjaanController::class);
    Route::post('jenis-pekerjaan/{jenisPekerjaan}/toggle', [JenisPekerjaanController::class, 'toggle'])
        ->name('jenis-pekerjaan.toggle');
    Route::resource('proyek', ProyekController::class);
    Route::delete('proyek/{proyek}/cover', [ProyekController::class, 'removeCover'])
        ->name('proyek.cover.destroy');
    Route::resource('contract', ContractController::class);
    Route::resource('project-assignment', ProjectAssignmentController::class);
    Route::get('/laporan-dokumentasi', [LaporanDokumentasiController::class, 'index'])
        ->name('laporan-dokumentasi.index');

    Route::resource('kategori-dokumen', KategoriDokumenController::class)
        ->except(['show'])
        ->parameters(['kategori-dokumen' => 'kategoriDokumen']);
    Route::resource('dokumen-proyek', DokumenProyekController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
        ->parameters(['dokumen-proyek' => 'dokumenProyek']);
    Route::post('dokumen-proyek/{dokumenProyek}/versi', [VersiDokumenController::class, 'store'])
        ->name('dokumen-proyek.versi.store');
    Route::get('versi-dokumen/{versiDokumen}/view', [VersiDokumenController::class, 'view'])
        ->name('versi-dokumen.view');
    Route::get('versi-dokumen/{versiDokumen}/download', [VersiDokumenController::class, 'download'])
        ->name('versi-dokumen.download');

    // Preview sementara - hanya untuk development - ongoing.
    Route::post('progress-proyek/{progress_proyek}/dokumentasi', [ProgressProyekController::class, 'storeDocumentation'])
        ->name('progress-proyek.dokumentasi.store');
    Route::get('progress-proyek/{progress_proyek}/dokumentasi/{dokumentasi}/view', [ProgressProyekController::class, 'viewDocumentation'])
        ->name('progress-proyek.dokumentasi.view');
    Route::get('progress-proyek/{progress_proyek}/dokumentasi/{dokumentasi}/download', [ProgressProyekController::class, 'downloadDocumentation'])
        ->name('progress-proyek.dokumentasi.download');
    Route::resource('progress-proyek', ProgressProyekController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
