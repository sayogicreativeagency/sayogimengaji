<?php

namespace App\Http\Controllers;

use App\Models\Doa;

class DoaController extends Controller
{
    public function daftar()
    {
        return view('doa.daftar', ['grup' => Doa::orderBy('id')->get()->groupBy('grup')]);
    }

    public function tampil(Doa $doa)
    {
        return view('doa.tampil', [
            'doa' => $doa,
            'lainnya' => Doa::where('grup', $doa->grup)->whereKeyNot($doa->id)->orderBy('id')->get(['id', 'nama']),
        ]);
    }
}
