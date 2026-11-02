<?php

namespace App\Actions;

use App\Ai\Agents\PerapiSugesti;
use App\Models\PencarianHarian;
use App\Models\Pengaturan;
use Illuminate\Support\Str;
use Throwable;

/**
 * Sugesti pencarian harian: kata terbanyak 30 hari terakhir → dirapikan LLM → `pengaturan.sugesti_cari`.
 * Kosong = data belum cukup; beranda memakai daftar bawaan (komponen contoh-cari).
 */
final class BuatSugestiCari
{
    /** ponytail: ambang tetap supaya pencarian pribadi satu orang tidak tampil; naikkan kalau sugesti terasa acak. */
    public const MINIMAL_DICARI = 3;

    public const JUMLAH = 8;

    /** @return list<string> */
    public function __invoke(): array
    {
        $sembunyi = Pengaturan::ambil('sugesti_disembunyikan', []);
        $kata = PencarianHarian::teratas(minimal: self::MINIMAL_DICARI)
            ->reject(fn ($b) => in_array($b->kata, $sembunyi, true))
            ->values();

        if ($kata->count() < self::JUMLAH / 2) {
            Pengaturan::simpan('sugesti_cari', []);

            return [];
        }

        try {
            $mentah = (new PerapiSugesti)->prompt($kata->map(fn ($b) => "{$b->kata} ({$b->jumlah})")->implode("\n"))['sugesti'];
        } catch (Throwable $e) {
            report($e);
            $mentah = $kata->pluck('kata')->all();
        }

        $sugesti = collect($mentah)
            ->map(fn ($s) => Str::of((string) $s)->squish()->lower()->rtrim('.?!')->limit(60, '')->toString())
            ->filter()
            ->reject(fn ($s) => in_array($s, $sembunyi, true))
            ->unique()
            ->take(self::JUMLAH)
            ->values()
            ->all();

        Pengaturan::simpan('sugesti_cari', $sugesti);

        return $sugesti;
    }
}
