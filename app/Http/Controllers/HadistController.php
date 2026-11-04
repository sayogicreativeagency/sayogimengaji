<?php

namespace App\Http\Controllers;

use App\Actions\CariHadist;
use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Enums\Qori;
use App\Models\Hadist;
use App\Models\PencarianHarian;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HadistController extends Controller
{
    public function beranda()
    {
        return view('beranda', [
            'daftarSurat' => Surat::orderBy('nomor')->get(['nomor', 'nama', 'nama_latin', 'arti', 'jumlah_ayat']),
            'qori' => collect(Qori::cases())->map(fn (Qori $q) => ['id' => $q->value, 'url' => $q->urlAudio()]),
        ]);
    }

    /** /hadist: form pencarian, penjelasan derajat, dan daftar kitab beserta jumlah hadistnya. */
    public function pencarian()
    {
        // Data hadist hanya berubah lewat impor atau koreksi admin, jadi hitungan cukup diperbarui sehari sekali.
        $baris = collect(Cache::remember('jumlah-hadist', now()->addDay(), fn () => Hadist::toBase()
            ->selectRaw('kitab, derajat_ringkas, count(*) as n')
            ->groupBy('kitab', 'derajat_ringkas')
            ->get()
            ->map(fn ($b) => (array) $b)
            ->all()));

        return view('pencarian', [
            'total' => $baris->sum('n'),
            'perKitab' => $baris->groupBy('kitab')->map->sum('n'),
            'perDerajat' => $baris->groupBy(fn ($b) => $b['derajat_ringkas'] ?? 'belum')->map->sum('n'),
        ]);
    }

    public function cari(Request $request, CariHadist $cari)
    {
        $q = Str::limit(Str::squish((string) $request->query('q')), 200, '');

        if ($q === '') {
            return redirect('/');
        }

        $hasil = $cari($q);
        // Bahan sugesti & riset pencarian; gagal mencatat tidak boleh menggagalkan pencarian.
        rescue(fn () => PencarianHarian::catat($q, $hasil->hadist->isEmpty()));

        return view('cari', ['q' => $q, 'hasil' => $hasil]);
    }

    /** Potongan HTML untuk filter AJAX di halaman /cari; tanpa panggilan AI. */
    public function saring(Request $request, CariHadist $cari)
    {
        $q = Str::limit(Str::squish((string) $request->query('q')), 200, '');
        abort_if($q === '', 422);

        $hadist = $cari->saring(
            $q,
            Derajat::tryFrom((string) $request->query('derajat')),
            Kitab::tryFrom((string) $request->query('kitab')),
        );

        return view('partials.daftar-hadist', [
            'hadist' => $hadist,
            'keterangan' => $hadist->count().' hadist cocok dengan filter'.($hadist->count() >= 30 ? ' (30 teratas)' : '').'.',
        ]);
    }

    public function tampil(Kitab $kitab, string $nomor)
    {
        return view('hadist', [
            'hadist' => Hadist::where('kitab', $kitab)->where('nomor', $nomor)->firstOrFail(),
        ]);
    }
}
