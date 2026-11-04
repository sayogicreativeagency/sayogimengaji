<?php

namespace App\Http\Controllers;

use App\Enums\Qori;
use App\Models\PutarMurottal;
use App\Models\Surat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuranController extends Controller
{
    /** Laporan pemutar murottal (statistik admin); baris sesi yang sama diperbarui, identitas awalnya tetap. */
    public function putar(Request $request)
    {
        $surat = Surat::findOrFail($request->integer('surat'));
        $data = $request->validate([
            'sesi' => ['required', 'uuid'],
            'ayat_mulai' => ['required', 'integer', 'min:0', "max:{$surat->jumlah_ayat}"],
            'ayat_terakhir' => ['required', 'integer', 'min:0', "max:{$surat->jumlah_ayat}"],
            'detik' => ['required', 'integer', 'min:0', 'max:86400'],
            'qori' => ['required', Rule::enum(Qori::class)],
            'sumber' => ['required', 'in:surat,beranda'],
            'selesai' => ['required', 'boolean'],
        ]);

        PutarMurottal::upsert([[
            ...$data,
            'selesai' => $request->boolean('selesai'),
            'surat_nomor' => $surat->nomor,
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
            'perangkat' => preg_match('/Mobi|Android/i', (string) $request->userAgent()) ? 'HP' : 'Komputer',
        ]], ['sesi'], ['ayat_terakhir', 'detik', 'qori', 'selesai']);

        return response()->noContent();
    }

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
