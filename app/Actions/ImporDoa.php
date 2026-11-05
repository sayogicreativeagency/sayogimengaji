<?php

namespace App\Actions;

use App\Models\Doa;
use Illuminate\Support\Facades\Http;

/** Impor doa dan dzikir dari equran.id. Doa tanpa arti dilewati karena tidak layak ditampilkan. */
final class ImporDoa
{
    /** @return int jumlah doa yang di-upsert */
    public function __invoke(): int
    {
        $data = Http::timeout(60)->retry(2, 500)->get('https://equran.id/api/doa')->throw()->json('data');

        $baris = collect($data)
            ->filter(fn (array $d) => filled($d['ar']) && filled($d['idn']))
            ->map(fn (array $d) => [
                'id' => $d['id'],
                'grup' => rtrim(trim($d['grup']), '.'),
                'nama' => trim($d['nama']),
                'arab' => trim($d['ar']),
                'latin' => trim((string) $d['tr']),
                'arti' => trim($d['idn']),
                'tentang' => $this->bersihkanTentang($d['tentang'] ?? null),
            ])
            ->values()
            ->all();

        Doa::upsert($baris, ['id'], ['grup', 'nama', 'arab', 'latin', 'arti', 'tentang']);

        return count($baris);
    }

    /** Tautan foto Facebook bukan rujukan; buang barisnya, sisanya apa adanya. */
    private function bersihkanTentang(?string $teks): ?string
    {
        $teks = trim(preg_replace('~^Sumber:\s*https?://(www\.)?facebook\.com\S*\s*$~mi', '', (string) $teks));

        return $teks === '' ? null : $teks;
    }
}
