<?php

namespace App\Http\Controllers;

use App\Models\Doa;
use Illuminate\Http\Request;

class DoaController extends Controller
{
    public function daftar(Request $request)
    {
        return view('doa.daftar', [
            'grup' => Doa::orderBy('id')->get()->groupBy('grup'),
            'q' => is_string($request->query('q')) ? $request->query('q') : '', // ?q[]= tidak boleh jadi error
        ]);
    }

    public function tampil(Doa $doa)
    {
        return view('doa.tampil', [
            'doa' => $doa,
            'lainnya' => Doa::where('grup', $doa->grup)->whereKeyNot($doa->id)->orderBy('id')->get(['id', 'nama']),
        ]);
    }
}
