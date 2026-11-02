<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Satu baris per (tanggal, kata); tidak menyimpan siapa yang mencari. */
class PencarianHarian extends Model
{
    protected $table = 'pencarian_harian';

    protected $guarded = ['id'];

    public $timestamps = false;

    protected function casts(): array
    {
        return ['tanggal' => 'date'];
    }

    public static function catat(string $q, bool $tanpaHasil): void
    {
        $kata = Str::of($q)->squish()->lower()->limit(200, '')->toString();
        if ($kata === '') {
            return;
        }

        static::upsert(
            [['tanggal' => today()->toDateString(), 'kata' => $kata, 'jumlah' => 1, 'jumlah_tanpa_hasil' => (int) $tanpaHasil]],
            ['tanggal', 'kata'],
            ['jumlah' => DB::raw('jumlah + 1'), 'jumlah_tanpa_hasil' => DB::raw('jumlah_tanpa_hasil + '.(int) $tanpaHasil)],
        );
    }

    /**
     * Kata terbanyak dicari dalam `$hari` terakhir, dengan total jumlah dan jumlah tanpa hasil.
     *
     * @return Collection<int, object{kata: string, jumlah: int, tanpa_hasil: int}>
     */
    public static function teratas(int $hari = 30, int $batas = 50, int $minimal = 1): Collection
    {
        return static::query()->toBase()
            ->selectRaw('kata, sum(jumlah) as jumlah, sum(jumlah_tanpa_hasil) as tanpa_hasil')
            ->where('tanggal', '>', today()->subDays($hari)->toDateString())
            ->groupBy('kata')
            ->havingRaw('sum(jumlah) >= ?', [$minimal])
            ->orderByDesc('jumlah')
            ->orderBy('kata')
            ->limit($batas)
            ->get()
            ->map(fn ($b) => (object) ['kata' => $b->kata, 'jumlah' => (int) $b->jumlah, 'tanpa_hasil' => (int) $b->tanpa_hasil]);
    }
}
