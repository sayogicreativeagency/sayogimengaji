<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Satu baris per akun; aktif selama `berakhir_pada` belum lewat. Diperpanjang lewat Pembayaran yang disetujui admin atau command. */
class Langganan extends Model
{
    protected $table = 'langganan';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['berakhir_pada' => 'datetime'];
    }

    /** Saklar di admin. Mati = semua surat belajar gratis untuk akun yang login. */
    public static function berbayar(): bool
    {
        return (bool) Pengaturan::ambil('langganan_berbayar', true);
    }

    /** Harga per bulan dalam rupiah. */
    public static function harga(): int
    {
        return (int) Pengaturan::ambil('langganan_harga', 33000);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function aktif(): bool
    {
        return $this->berakhir_pada->isFuture();
    }
}
