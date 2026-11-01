<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Satu baris per akun; aktif selama `berakhir_pada` belum lewat. Pembayaran (Duitku/transfer) menyusul. */
class Langganan extends Model
{
    protected $table = 'langganan';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['berakhir_pada' => 'datetime'];
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
