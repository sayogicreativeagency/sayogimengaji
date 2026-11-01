<?php

namespace App\Actions;

use App\Models\Ayat;
use App\Models\Surat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/** Impor satu surat (teks Arab, latin, terjemahan Kemenag) dari equran.id. Audio tetap di CDN mereka. */
final class ImporQuran
{
    /** @return int jumlah ayat yang di-upsert */
    public function __invoke(int $nomor): int
    {
        $data = Http::timeout(60)->retry(2, 500)->get("https://equran.id/api/v2/surat/{$nomor}")->throw()->json('data');

        DB::transaction(function () use ($data) {
            Surat::updateOrCreate(['nomor' => $data['nomor']], [
                'nama' => $data['nama'],
                'nama_latin' => $data['namaLatin'],
                'arti' => $data['arti'],
                'tempat_turun' => $data['tempatTurun'],
                'jumlah_ayat' => $data['jumlahAyat'],
                'deskripsi' => $this->bersihkan($data['deskripsi']),
            ]);

            Ayat::upsert(array_map(fn (array $a) => [
                'surat_nomor' => $data['nomor'],
                'nomor' => $a['nomorAyat'],
                'teks_arab' => $a['teksArab'],
                'teks_latin' => trim($a['teksLatin']),
                'terjemahan' => trim($a['teksIndonesia']),
            ], $data['ayat']), ['surat_nomor', 'nomor'], ['teks_arab', 'teks_latin', 'terjemahan', 'updated_at']);
        });

        return count($data['ayat']);
    }

    /** Deskripsi dari API berisi HTML; sisakan hanya <i> dan <br> tanpa atribut. */
    private function bersihkan(string $html): string
    {
        return preg_replace(['/<i\b[^>]*>/i', '/<br\b[^>]*>/i'], ['<i>', '<br>'], strip_tags($html, '<i><br>'));
    }
}
