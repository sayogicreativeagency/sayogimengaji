<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Surat extends Model
{
    protected $table = 'surat';

    protected $primaryKey = 'nomor';

    public $incrementing = false;

    protected $guarded = [];

    /** @return HasMany<Ayat, $this> */
    public function ayat(): HasMany
    {
        return $this->hasMany(Ayat::class, 'surat_nomor')->orderBy('nomor');
    }

    /** Basmalah dibaca sebagai pembuka, kecuali Al-Fatihah (sudah ayat 1) dan At-Taubah. */
    public function pakaiBasmalah(): bool
    {
        return ! in_array($this->nomor, [1, 9], true);
    }
}
