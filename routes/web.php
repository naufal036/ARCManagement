
<?php

use App\Http\Controllers\JenisPekerjaanController;
use App\Http\Controllers\KlienController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProyekController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProjectAssignmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SystemConfigController;
use App\Http\Controllers\DokumenProyekController;
use App\Http\Controllers\KategoriDokumenController;
use App\Http\Controllers\VersiDokumenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('klien', KlienController::class);
    Route::resource('jenis-pekerjaan', JenisPekerjaanController::class);
    Route::resource('proyek', ProyekController::class);

    Route::resource('contract', ContractController::class);
    Route::resource('project-assignment', ProjectAssignmentController::class);

    Route::middleware(['auth', 'role:Admin'])->group(function () {
        Route::resource('users', UserController::class);

        // Tambahkan baris ini
        Route::get('/settings', [SystemConfigController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemConfigController::class, 'update'])->name('settings.update');
    });

    Route::resource('kategori-dokumen', KategoriDokumenController::class)
    ->except(['show'])
    ->parameters(['kategori-dokumen' => 'kategoriDokumen']);

    Route::resource('dokumen-proyek', DokumenProyekController::class);
    Route::post('dokumen-proyek/{dokumenProyek}/versi', [VersiDokumenController::class, 'store'])
        ->name('dokumen-proyek.versi.store');
    Route::get('versi-dokumen/{versiDokumen}/unduh', [VersiDokumenController::class, 'download'])
        ->name('versi-dokumen.download');
});

require __DIR__ . '/auth.php';
