<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\BelajarController;
use App\Http\Controllers\HadistController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\KontakController;
use App\Http\Controllers\LanggananController;
use App\Http\Controllers\QuranController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HadistController::class, 'beranda']);
Route::get('/hadist', [HadistController::class, 'pencarian']);
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
Route::view('/syarat-ketentuan', 'legal.syarat');
Route::view('/kebijakan-privasi', 'legal.privasi');
Route::view('/sumber', 'legal.sumber');
Route::view('/tentang', 'tentang');
Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);
Route::get('/kalender', [KalenderController::class, 'tampil']);

Route::middleware('auth')->prefix('belajar')->group(function () {
    Route::get('/', [BelajarController::class, 'dasbor']);
    Route::post('/progres', [BelajarController::class, 'progres'])->middleware('throttle:120,1');
    Route::get('/{surat}', [BelajarController::class, 'tampil'])->whereNumber('surat');
});

Route::middleware('auth')->prefix('langganan')->group(function () {
    Route::get('/', [LanggananController::class, 'tampil']);
    Route::post('/', [LanggananController::class, 'pesan'])->middleware('throttle:10,1');
    Route::get('/bayar/{pembayaran}', [LanggananController::class, 'bayar']);
    Route::post('/bayar/{pembayaran}', [LanggananController::class, 'konfirmasi'])->middleware('throttle:10,1');
});
