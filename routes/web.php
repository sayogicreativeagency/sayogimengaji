<?php

use App\Http\Controllers\HadistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HadistController::class, 'beranda']);
// throttle: setiap pencarian baru memanggil AI berbayar.
Route::get('/cari', [HadistController::class, 'cari'])->middleware('throttle:20,1');
Route::get('/hadist/{kitab}/{nomor}', [HadistController::class, 'tampil'])->name('hadist');
