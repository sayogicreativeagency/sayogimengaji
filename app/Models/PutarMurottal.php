<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/** Satu sesi putar murottal: dari audio pertama berjalan sampai surat tamat atau pendengar pergi. */
class PutarMurottal extends Model
{
    public const PERIODE = ['24jam' => '24 jam terakhir', 'hari_ini' => 'Hari ini', '7hari' => '7 hari terakhir'];

    protected $table = 'putar_murottal';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['selesai' => 'boolean'];
    }

    /** @return BelongsTo<Surat, $this> */
    public function surat(): BelongsTo
    {
        return $this->belongsTo(Surat::class, 'surat_nomor');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** "Hari ini" mengikuti WIB, sedangkan waktu tersimpan memakai zona waktu aplikasi. */
    public static function sejak(string $periode): CarbonInterface
    {
        return match ($periode) {
            '24jam' => now()->subDay(),
            'hari_ini' => now('Asia/Jakarta')->startOfDay()->setTimezone(config('app.timezone')),
            '7hari' => now()->subDays(7),
        };
    }

    /** @param  Builder<self>  $query */
    public function scopeDalam(Builder $query, string $periode): void
    {
        $query->where('created_at', '>=', static::sejak($periode));
    }

    /** @return object{diputar: int, selesai: int, pendengar: int, detik: int} */
    public static function ringkasan(string $periode): object
    {
        $r = static::query()->dalam($periode)->toBase()
            ->selectRaw('count(*) as diputar, sum(selesai) as selesai, count(distinct ip) as pendengar, sum(detik) as detik')
            ->first();

        return (object) ['diputar' => (int) $r->diputar, 'selesai' => (int) $r->selesai, 'pendengar' => (int) $r->pendengar, 'detik' => (int) $r->detik];
    }

    /**
     * Surat terbanyak diputar, dengan ayat tempat pendengar paling sering berhenti sebelum tamat.
     *
     * @return Collection<int, self>
     */
    public static function perSurat(string $periode, int $batas = 20): Collection
    {
        $berhenti = static::query()->dalam($periode)->where('selesai', false)->toBase()
            ->selectRaw('surat_nomor, ayat_terakhir, count(*) as jumlah')
            ->groupBy('surat_nomor', 'ayat_terakhir')
            ->orderByDesc('jumlah')
            ->orderBy('ayat_terakhir')
            ->get()
            ->unique('surat_nomor')
            ->keyBy('surat_nomor');

        return static::query()->dalam($periode)->with('surat')
            ->selectRaw('surat_nomor, count(*) as diputar, sum(selesai) as jumlah_selesai, count(distinct ip) as pendengar')
            ->groupBy('surat_nomor')
            ->orderByDesc('diputar')
            ->orderBy('surat_nomor')
            ->limit($batas)
            ->get()
            ->each(fn (self $b) => $b->berhenti_terbanyak = $berhenti->get($b->surat_nomor));
    }

    /** @return array<string, int> nilai kolom → jumlah sesi, terbanyak dulu */
    public static function rincian(string $periode, string $kolom): array
    {
        return static::query()->dalam($periode)->toBase()
            ->selectRaw("{$kolom} as nilai, count(*) as jumlah")
            ->groupBy($kolom)
            ->orderByDesc('jumlah')
            ->pluck('jumlah', 'nilai')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    public static function labelAyat(int $ayat): string
    {
        return $ayat === 0 ? 'Basmalah' : "Ayat {$ayat}";
    }

    public static function durasi(int $detik): string
    {
        return $detik >= 3600
            ? floor($detik / 3600).' jam '.floor($detik % 3600 / 60).' mnt'
            : floor($detik / 60).' mnt '.($detik % 60).' dtk';
    }
}
