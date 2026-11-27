<?php

use App\Http\Controllers\ContractAddendumController;
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
use App\Http\Controllers\ProjectTaskController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\SystemConfigController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VersiDokumenController;
use App\Http\Controllers\ChecklistItemController;
use App\Http\Controllers\UserReportController;
use App\Http\Controllers\WeeklyDocumentationController;
use App\Http\Controllers\WeeklyDocumentationPhotoController;
use App\Http\Controllers\LaporanPengawasanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Administrasi (Khusus Admin)
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware(['role:Admin,Direktur,Staff Administrasi,Pengawas Lapangan,Manajer,Karyawan'])->group(function () {
        Route::get('/settings', [SystemConfigController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemConfigController::class, 'update'])->name('settings.update');
    });

    // Data Master & Operasional Proyek
    Route::resource('klien', KlienController::class);
    Route::resource('jenis-pekerjaan', JenisPekerjaanController::class);
    Route::post('jenis-pekerjaan/{jenisPekerjaan}/toggle', [JenisPekerjaanController::class, 'toggle'])
        ->name('jenis-pekerjaan.toggle');
    Route::resource('proyek', ProyekController::class);
    Route::delete('proyek/{proyek}/cover', [ProyekController::class, 'removeCover'])
        ->name('proyek.cover.destroy');
    Route::resource('contract', ContractController::class);
    Route::resource('project-assignment', ProjectAssignmentController::class);

    // Document Management
    Route::get('/laporan-dokumentasi', [LaporanDokumentasiController::class, 'index'])
        ->name('laporan-dokumentasi.index');
    Route::resource('laporan-pengawasan', LaporanPengawasanController::class)
        ->parameters(['laporan-pengawasan' => 'laporanPengawasan']);
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

    // Progress Reporting & Dokumentasi Lapangan
    Route::post('progress-proyek/{progress_proyek}/dokumentasi', [ProgressProyekController::class, 'storeDocumentation'])
        ->name('progress-proyek.dokumentasi.store');
    Route::get('progress-proyek/{progress_proyek}/dokumentasi/{dokumentasi}/view', [ProgressProyekController::class, 'viewDocumentation'])
        ->name('progress-proyek.dokumentasi.view');
    Route::get('progress-proyek/{progress_proyek}/dokumentasi/{dokumentasi}/download', [ProgressProyekController::class, 'downloadDocumentation'])
        ->name('progress-proyek.dokumentasi.download');
    Route::resource('progress-proyek', ProgressProyekController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // ===== Kontrak & Assignment =====
    Route::get('contract/{contract}/file', [ContractController::class, 'download'])
        ->name('contract.file.download');
    Route::post('contract/{contract}/addendums', [ContractAddendumController::class, 'store'])
        ->name('contract.addendums.store');
    Route::put('contract/{contract}/addendums/{contractAddendum}', [ContractAddendumController::class, 'update'])
        ->name('contract.addendums.update');
    Route::delete('contract/{contract}/addendums/{contractAddendum}', [ContractAddendumController::class, 'destroy'])
        ->name('contract.addendums.destroy');
    Route::get('contract/{contract}/addendums/{contractAddendum}/file', [ContractAddendumController::class, 'download'])
        ->name('contract.addendums.file.download');
    Route::resource('project-task', ProjectTaskController::class);

    Route::get('/reports', [UserReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [UserReportController::class, 'store'])->name('reports.store');
    Route::put('/reports/{report}', [UserReportController::class, 'update'])->name('reports.update');
    Route::delete('/reports/{report}', [UserReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('/proyek/{proyek}/checklist', [ChecklistItemController::class, 'index'])->name('checklist.index');
    Route::post('/proyek/{proyek}/checklist', [ChecklistItemController::class, 'store'])->name('checklist.store');
    Route::put('/proyek/{proyek}/checklist/{checklist}', [ChecklistItemController::class, 'update'])->name('checklist.update');
    Route::delete('/proyek/{proyek}/checklist/{checklist}', [ChecklistItemController::class, 'destroy'])->name('checklist.destroy');

    Route::get('/weekly-documentation', [WeeklyDocumentationController::class, 'index'])->name('weekly-documentation.index');
    Route::post('/proyek/{proyek}/weekly-documentation', [WeeklyDocumentationController::class, 'store'])->name('weekly-documentation.store');
    Route::get('/weekly-documentation/{weeklyDocumentation}', [WeeklyDocumentationController::class, 'show'])->name('weekly-documentation.show');
    Route::post('/weekly-documentation/{weeklyDocumentation}/photos', [WeeklyDocumentationPhotoController::class, 'update'])->name('weekly-documentation.photos.update');
    Route::post('/weekly-documentation/photos', [WeeklyDocumentationPhotoController::class, 'store'])->name('weekly-documentation.photos.store');
    Route::get('/weekly-documentation/photos/{photo}/view', [WeeklyDocumentationPhotoController::class, 'view'])->name('weekly-documentation.photos.view');
    Route::patch('/weekly-documentation/photos/{photo}', [WeeklyDocumentationPhotoController::class, 'updateSingle'])->name('weekly-documentation.photos.update-single');
    Route::delete('/weekly-documentation/photos/{photo}', [WeeklyDocumentationPhotoController::class, 'destroy'])->name('weekly-documentation.photos.destroy');
});

require __DIR__.'/auth.php';
