<?php

namespace App\Actions;

use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;
use Illuminate\Support\Facades\Http;

/**
 * Impor satu kitab dari dataset fawazahmed0/hadith-api (edisi Indonesia + Arab).
 * Hadist yang sudah dikoreksi admin (dikoreksi_at terisi) tidak disentuh.
 */
final class ImporHadist
{
    private const URL = 'https://raw.githubusercontent.com/fawazahmed0/hadith-api/1/editions/%s-%s.min.json';

    /** @return int jumlah hadist yang di-upsert */
    public function __invoke(Kitab $kitab): int
    {
        $ind = $this->unduh('ind', $kitab);
        $arab = collect($this->unduh('ara', $kitab)['hadiths'])->keyBy(fn (array $h) => (string) $h['hadithnumber']);
        $bab = $ind['metadata']['sections'] ?? [];
        $dikoreksi = Hadist::where('kitab', $kitab)->whereNotNull('dikoreksi_at')->pluck('nomor')->flip();

        $baris = collect($ind['hadiths'])
            ->reject(fn (array $h) => $dikoreksi->has((string) $h['hadithnumber']))
            ->map(function (array $h) use ($kitab, $arab, $bab) {
                $nomor = (string) $h['hadithnumber'];
                $babNomor = $h['reference']['book'] ?? null;

                return [
                    'kitab' => $kitab->value,
                    'nomor' => $nomor,
                    'nomor_arab' => isset($h['arabicnumber']) ? (string) $h['arabicnumber'] : null,
                    'bab_nomor' => $babNomor,
                    'bab_judul' => ($bab[$babNomor] ?? '') ?: null,
                    'teks_arab' => $arab[$nomor]['text'] ?? null,
                    'terjemahan' => trim($h['text']),
                    'derajat' => json_encode(
                        array_map(fn (array $g) => ['ulama' => $g['name'], 'derajat' => $g['grade']], $h['grades']),
                        JSON_UNESCAPED_UNICODE,
                    ),
                    'derajat_ringkas' => Derajat::dariPenilaian($kitab, $h['grades'])?->value,
                ];
            });

        $kolom = ['nomor_arab', 'bab_nomor', 'bab_judul', 'teks_arab', 'terjemahan', 'derajat', 'derajat_ringkas', 'updated_at'];
        $baris->chunk(500)->each(fn ($batch) => Hadist::upsert($batch->values()->all(), ['kitab', 'nomor'], $kolom));

        return $baris->count();
    }

    private function unduh(string $bahasa, Kitab $kitab): array
    {
        return Http::timeout(120)->retry(2, 1000)->get(sprintf(self::URL, $bahasa, $kitab->value))->throw()->json();
    }
}
