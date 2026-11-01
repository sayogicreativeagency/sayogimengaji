<?php

namespace App\Models;

use App\Enums\Derajat;
use App\Enums\Kitab;
use Database\Factories\HadistFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hadist extends Model
{
    /** @use HasFactory<HadistFactory> */
    use HasFactory;

    protected $table = 'hadist';

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kitab' => Kitab::class,
            'derajat' => 'array',
            'derajat_ringkas' => Derajat::class,
            'dikoreksi_at' => 'datetime',
        ];
    }

    /**
     * FULLTEXT boolean (OR) atas terjemahan + judul bab, diurutkan menurut skor.
     *
     * @param  list<string>  $kataKunci
     */
    public function scopeCocok(Builder $query, array $kataKunci): void
    {
        $istilah = collect($kataKunci)
            ->map(fn (string $k) => trim(preg_replace('/[+\-<>()~*"@]+/', ' ', $k)))
            ->filter()
            ->map(fn (string $k) => str_contains($k, ' ') ? "\"{$k}\"" : "{$k}*")
            ->implode(' ');

        if ($istilah === '') {
            $query->whereRaw('0 = 1');

            return;
        }

        $query->whereFullText(['terjemahan', 'bab_judul'], $istilah, ['mode' => 'boolean'])
            ->orderByRaw('MATCH(terjemahan, bab_judul) AGAINST(? IN BOOLEAN MODE) DESC', [$istilah]);
    }
}
