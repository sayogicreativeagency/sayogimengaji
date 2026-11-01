<?php

namespace App\Actions;

use App\Ai\Agents\PemilihHadist;
use App\Ai\Agents\PengembangKataKunci;
use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

/**
 * Cari hadist: AI mengembangkan kata kunci → FULLTEXT → AI memilih & meringkas.
 * AI hanya memilih id; semua teks hadist yang ditampilkan berasal dari DB.
 */
final class CariHadist
{
    private const KANDIDAT = 30;

    private const HASIL = 10;

    public function __invoke(string $q, ?Derajat $derajat = null, ?Kitab $kitab = null): HasilPencarian
    {
        $q = Str::of($q)->squish()->lower()->toString();
        $kunci = 'cari:'.md5(json_encode([$q, $derajat?->value, $kitab?->value]));

        if ($simpanan = Cache::get($kunci)) {
            return new HasilPencarian($simpanan['ringkasan'], $this->muat($simpanan['ids']), true);
        }

        $aiTersedia = true;

        try {
            $kataKunci = (new PengembangKataKunci)->prompt($q)['kata_kunci'];
        } catch (Throwable $e) {
            report($e);
            $aiTersedia = false;
            $kataKunci = explode(' ', $q);
        }

        $kandidat = Hadist::query()
            ->cocok($kataKunci)
            ->when($derajat, fn ($query) => $query->where('derajat_ringkas', $derajat))
            ->when($kitab, fn ($query) => $query->where('kitab', $kitab))
            ->limit(self::KANDIDAT)
            ->get();

        if ($kandidat->isEmpty()) {
            return new HasilPencarian(null, $kandidat, $aiTersedia);
        }

        try {
            $pilihan = (new PemilihHadist)->prompt($this->promptPemilih($q, $kandidat));
        } catch (Throwable $e) {
            report($e);

            return new HasilPencarian(null, $kandidat->take(self::HASIL)->values(), false);
        }

        $ids = collect($pilihan['hadist_ids'])->map(fn ($id) => (int) $id)
            ->intersect($kandidat->modelKeys())->unique()->take(self::HASIL)->values()->all();
        $ringkasan = preg_replace_callback('/\[#(\d+)\]/', fn ($m) => in_array((int) $m[1], $ids, true) ? $m[0] : '', $pilihan['ringkasan']);

        if ($aiTersedia) {
            Cache::put($kunci, ['ringkasan' => $ringkasan, 'ids' => $ids], now()->addDays(7));
        }

        return new HasilPencarian($ringkasan, $this->muat($ids), $aiTersedia);
    }

    private function promptPemilih(string $q, Collection $kandidat): string
    {
        $daftar = $kandidat->map(fn (Hadist $h) => sprintf(
            '[#%d] (%s, HR. %s no. %s) %s',
            $h->id,
            $h->derajat_ringkas?->label() ?? 'belum dinilai',
            $h->kitab->perawi(),
            $h->nomor,
            $this->potongMatan($h->terjemahan),
        ))->implode("\n\n");

        return "Pertanyaan: {$q}\n\nKandidat:\n{$daftar}";
    }

    /**
     * Terjemahan diawali sanad panjang; matan ada di akhir, jadi ambil bagian akhirnya.
     * ponytail: heuristik 600 karakter terakhir, pisahkan sanad/matan saat impor kalau kurang akurat.
     */
    private function potongMatan(string $teks): string
    {
        return mb_strlen($teks) > 600 ? '…'.mb_substr($teks, -600) : $teks;
    }

    /** @param  list<int>  $ids */
    private function muat(array $ids): Collection
    {
        return Hadist::findMany($ids)->sortBy(fn (Hadist $h) => array_search($h->id, $ids))->values();
    }
}
