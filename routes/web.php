<?php

use App\Http\Controllers\HadistController;
use App\Http\Controllers\KontakController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HadistController::class, 'beranda']);
// throttle: setiap pencarian baru memanggil AI berbayar.
Route::get('/cari', [HadistController::class, 'cari'])->middleware('throttle:20,1');
Route::get('/cari/saring', [HadistController::class, 'saring'])->middleware('throttle:60,1');
Route::get('/hadist/{kitab}/{nomor}', [HadistController::class, 'tampil'])->name('hadist');
Route::get('/kontak', [KontakController::class, 'create']);
Route::post('/kontak', [KontakController::class, 'store'])->middleware('throttle:5,1');
Route::view('/dukung', 'dukung');
