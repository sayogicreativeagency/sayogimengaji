<?php

namespace App\Http\Controllers;

use App\Models\Surat;

class QuranController extends Controller
{
    public function daftar()
    {
        return view('quran.daftar', ['daftar' => Surat::orderBy('nomor')->get()]);
    }

    public function tampil(Surat $surat)
    {
        return view('quran.surat', [
            'surat' => $surat->load('ayat'),
            'sebelumnya' => Surat::find($surat->nomor - 1),
            'berikutnya' => Surat::find($surat->nomor + 1),
        ]);
    }
}
