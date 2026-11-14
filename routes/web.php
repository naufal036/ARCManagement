<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SystemConfigController;
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

    Route::middleware(['auth', 'role:Admin'])->group(function () {
        Route::resource('users', UserController::class);

        // Tambahkan baris ini
        Route::get('/settings', [SystemConfigController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SystemConfigController::class, 'update'])->name('settings.update');
    });
});

require __DIR__ . '/auth.php';
