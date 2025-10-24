<?php

use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ProfileController;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/test-notifikasi', function () {
    Notification::make()
        ->title('Berhasil!')
        ->body('Jika Anda melihat ini, artinya Filament Notifications sudah berfungsi.')
        ->success() // Tipe notifikasi (success, warning, danger)
        ->send(); // Kirim notifikasi

    return redirect()->route('dashboard');
})->middleware('auth')->name('test.notification');

// Rute aplikasi utama
Route::middleware('auth')->group(function () {
    
    // 2. TAMBAHKAN ROUTE RESOURCE INI
    // Ini akan otomatis membuat route untuk:
    // - GET /penjualan (index)
    // - GET /penjualan/create (create)
    // - POST /penjualan (store)
    // - GET /penjualan/{id} (show)
    // - GET /penjualan/{id}/edit (edit)
    // - PUT/PATCH /penjualan/{id} (update)
    // - DELETE /penjualan/{id} (destroy)
    Route::resource('penjualan', PenjualanController::class);

});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
