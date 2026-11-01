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

        $derajat = Derajat::tryFrom((string) $request->query('derajat'));
        $kitab = Kitab::tryFrom((string) $request->query('kitab'));

        return view('cari', [
            'q' => $q,
            'derajat' => $derajat,
            'kitab' => $kitab,
            'hasil' => $cari($q, $derajat, $kitab),
        ]);
    }

    public function tampil(Kitab $kitab, string $nomor)
    {
        return view('hadist', [
            'hadist' => Hadist::where('kitab', $kitab)->where('nomor', $nomor)->firstOrFail(),
        ]);
    }
}
