<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Pengaturan situs kunci → nilai (JSON), diubah dari panel admin. */
class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $primaryKey = 'kunci';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['nilai' => 'array'];
    }

    public static function ambil(string $kunci, mixed $bawaan = null): mixed
    {
        return static::find($kunci)?->nilai ?? $bawaan;
    }

    public static function simpan(string $kunci, mixed $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
    }
}
