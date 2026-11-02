<?php

namespace App\Http\Controllers;

use App\Enums\QoriBelajar;
use App\Models\Kata;
use App\Models\Langganan;
use App\Models\Surat;
use App\Models\WaktuKata;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Fitur belajar/hafalan Al-Qur'an (langganan; Al-Fatihah gratis; semua gratis bila saklar langganan mati). */
class BelajarController extends Controller
{
    public function dasbor(Request $request)
    {
        $user = $request->user();
        $progres = $user->progresBelajar()->get();
        // Tie-breaker id: dua progres bisa tersimpan di detik yang sama.
        $terakhir = $user->progresBelajar()->latest('updated_at')->latest('id')->first();

        return view('belajar.dasbor', [
            'daftar' => Surat::orderBy('nomor')->get(),
            'hafal' => $progres->whereNotNull('dihafal_at')->countBy('surat_nomor'),
            'terakhir' => $terakhir,
            'langganan' => $user->berlangganan() ? $user->langganan : null,
            'berbayar' => Langganan::berbayar(),
        ]);
    }

    public function tampil(Request $request, Surat $surat)
    {
        if (! $request->user()->bisaBelajar($surat->nomor)) {
            return view('belajar.terkunci', ['surat' => $surat, 'harga' => Langganan::harga()]);
        }

        $kata = Kata::where('surat_nomor', $surat->nomor)->orderBy('ayat_nomor')->orderBy('posisi')->get()->groupBy('ayat_nomor');
        // waktu[ayat][qori] = [[posisi, mulai, selesai], ...] urut posisi kata.
        $qori = QoriBelajar::aktif();
        $waktu = WaktuKata::where('surat_nomor', $surat->nomor)->whereIn('qori', array_column($qori, 'value'))->orderBy('posisi')->get()
            ->groupBy(['ayat_nomor', 'qori'])
            ->map(fn ($perQori) => $perQori->map(fn ($baris) => $baris->map(fn ($w) => [$w->posisi, $w->mulai_ms, $w->selesai_ms])->values()));

        return view('belajar.surat', [
            'surat' => $surat->load('ayat'),
            'kata' => $kata,
            'waktu' => $waktu,
            'hafal' => $request->user()->progresBelajar()->where('surat_nomor', $surat->nomor)->whereNotNull('dihafal_at')->pluck('ayat_nomor')->flip(),
            'qori' => collect($qori)->map(fn ($q) => ['id' => $q->value, 'nama' => $q->nama(), 'url' => $q->urlAudio()]),
            'berikutnya' => Surat::find($surat->nomor + 1),
        ]);
    }

    public function progres(Request $request)
    {
        $data = $request->validate([
            'surat' => ['required', 'integer', 'exists:surat,nomor'],
            'ayat' => ['required', 'integer', 'min:1'],
            'status' => ['required', Rule::in(['didengar', 'hafal', 'belum-hafal'])],
        ]);
        abort_unless($request->user()->bisaBelajar($data['surat']), 403);
        $request->validate(['ayat' => ['integer', 'max:'.Surat::find($data['surat'])->jumlah_ayat]]);

        $progres = $request->user()->progresBelajar()->firstOrNew(['surat_nomor' => $data['surat'], 'ayat_nomor' => $data['ayat']]);
        match ($data['status']) {
            'didengar' => $progres->didengar_at ??= now(),
            'hafal' => $progres->dihafal_at = now(),
            'belum-hafal' => $progres->dihafal_at = null,
        };
        $progres->touch(); // tetap perbarui updated_at = posisi terakhir

        return response()->noContent();
    }
}
