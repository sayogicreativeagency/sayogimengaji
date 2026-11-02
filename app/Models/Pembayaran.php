<?php

namespace App\Models;

use App\Enums\StatusBayar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use LogicException;

/** Tagihan langganan; admin mencocokkan nominal unik dengan mutasi lalu menyetujui. */
class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $guarded = ['id'];

    public const PILIHAN_BULAN = [1, 3, 6, 12];

    protected function casts(): array
    {
        return ['bulan' => 'integer', 'nominal' => 'integer', 'status' => StatusBayar::class, 'dilaporkan_at' => 'datetime'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @param  Builder<self>  $query */
    public function scopeTerbuka(Builder $query): void
    {
        $query->whereIn('status', [StatusBayar::Menunggu, StatusBayar::Dilaporkan]);
    }

    /**
     * Tambah kode unik 1-999 yang belum dipakai tagihan terbuka, supaya transfer bisa dicocokkan dari nominalnya saja.
     * ponytail: macet bila ada 999 tagihan terbuka dengan harga dasar sama; ganti ke kode 4 digit kalau sampai sebanyak itu.
     */
    public static function nominalUnik(int $dasar): int
    {
        do {
            $nominal = $dasar + random_int(1, 999);
        } while (static::terbuka()->where('nominal', $nominal)->exists());

        return $nominal;
    }

    public function setujui(): void
    {
        DB::transaction(function () {
            $kunci = static::lockForUpdate()->findOrFail($this->id);
            if (! $kunci->status->terbuka()) {
                throw new LogicException("Pembayaran #{$this->id} sudah {$kunci->status->getLabel()}.");
            }
            $this->user->perpanjangLangganan($this->bulan, $this->metode ?? 'bayar', "Pembayaran #{$this->id}");
            $this->update(['status' => StatusBayar::Lunas]);
        });
    }
}
