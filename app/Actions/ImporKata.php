<?php

namespace App\Actions;

use App\Enums\QoriBelajar;
use App\Models\Kata;
use App\Models\WaktuKata;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/** Impor kata Utsmani + waktu tiap kata untuk satu surat & qori dari API Quran.com. */
final class ImporKata
{
    /** @return int jumlah kata yang di-upsert */
    public function __invoke(int $surat, QoriBelajar $qori): int
    {
        $ayat = Http::timeout(60)->retry(2, 500)->get("https://api.quran.com/api/v4/verses/by_chapter/{$surat}", [
            'words' => 'true',
            'word_fields' => 'text_uthmani',
            'audio' => $qori->idQuranCom(),
            'per_page' => 300,
        ])->throw()->json('verses');

        $kata = [];
        $waktu = [];
        foreach ($ayat as $a) {
            $nomor = (int) explode(':', $a['verse_key'])[1];
            $posisiKata = [];

            foreach ($a['words'] as $w) {
                if ($w['char_type_name'] !== 'word') {
                    continue; // tanda nomor ayat
                }
                $posisiKata[$w['position']] = true;
                $kata[] = ['surat_nomor' => $surat, 'ayat_nomor' => $nomor, 'posisi' => $w['position'], 'teks' => $w['text_uthmani']];
            }

            foreach ($a['audio']['segments'] ?? [] as $s) {
                // Format [indeks, posisi, mulai, selesai] atau [posisi, mulai, selesai].
                [$posisi, $mulai, $selesai] = count($s) >= 4 ? array_slice($s, 1, 3) : $s;
                if (isset($posisiKata[$posisi])) {
                    $waktu[] = [
                        'qori' => $qori->value, 'surat_nomor' => $surat, 'ayat_nomor' => $nomor,
                        'posisi' => $posisi, 'mulai_ms' => $mulai, 'selesai_ms' => $selesai,
                    ];
                }
            }
        }

        DB::transaction(function () use ($kata, $waktu) {
            foreach (array_chunk($kata, 1000) as $batch) {
                Kata::upsert($batch, ['surat_nomor', 'ayat_nomor', 'posisi'], ['teks']);
            }
            foreach (array_chunk($waktu, 1000) as $batch) {
                WaktuKata::upsert($batch, ['qori', 'surat_nomor', 'ayat_nomor', 'posisi'], ['mulai_ms', 'selesai_ms']);
            }
        });

        return count($kata);
    }
}
