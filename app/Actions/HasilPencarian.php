<?php

namespace App\Actions;

use App\Models\Hadist;
use Illuminate\Support\Collection;

final readonly class HasilPencarian
{
    /**
     * @param  ?string  $ringkasan  null = tanpa ringkasan (AI gagal atau tidak ada hasil)
     * @param  Collection<int, Hadist>  $hadist  urut relevansi
     */
    public function __construct(
        public ?string $ringkasan,
        public Collection $hadist,
        public bool $aiTersedia,
    ) {}
}
