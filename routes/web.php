<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PeramalanController;
use App\Http\Controllers\ProfileController;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});




// Rute aplikasi utama
Route::middleware('auth')->group(function () {
    Route::resource('penjualan', PenjualanController::class);

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('peramalan', [PeramalanController::class, 'index'])->name('peramalan.index');
    Route::post('peramalan/hitung', [PeramalanController::class, 'hitung'])->name('peramalan.hitung');

    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
