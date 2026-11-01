<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\HadistController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\QuranController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HadistController::class, 'beranda']); // nanti jadi landing; cari hadist pindah ke /hadist
Route::get('/hadist', [HadistController::class, 'beranda']);
// throttle: setiap pencarian baru memanggil AI berbayar.
Route::get('/cari', [HadistController::class, 'cari'])->middleware('throttle:20,1');
Route::get('/cari/saring', [HadistController::class, 'saring'])->middleware('throttle:60,1');
Route::get('/hadist/{kitab}/{nomor}', [HadistController::class, 'tampil'])->name('hadist');
Route::get('/kontak', [KontakController::class, 'create']);
Route::post('/kontak', [KontakController::class, 'store'])->middleware('throttle:5,1');
Route::view('/dukung', 'dukung');
Route::get('/quran', [QuranController::class, 'daftar']);
Route::get('/quran/{surat}', [QuranController::class, 'tampil'])->whereNumber('surat');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AkunController::class, 'formMasuk'])->name('login');
    Route::post('/masuk', [AkunController::class, 'masuk']);
    Route::get('/daftar', [AkunController::class, 'formDaftar']);
    Route::post('/daftar', [AkunController::class, 'daftar']);
});
Route::post('/keluar', [AkunController::class, 'keluar'])->middleware('auth');
