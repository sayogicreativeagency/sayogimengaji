<?php

namespace App\Http\Controllers;

use App\Actions\CariHadist;
use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HadistController extends Controller
{
    public function beranda()
    {
        return view('beranda');
    }

    public function cari(Request $request, CariHadist $cari)
    {
        $q = Str::limit(Str::squish((string) $request->query('q')), 200, '');

        if ($q === '') {
            return redirect('/');
        }

        return view('cari', ['q' => $q, 'hasil' => $cari($q)]);
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
