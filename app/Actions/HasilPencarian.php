<?php

namespace App\Actions;

use App\Models\Hadist;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

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

    /** Ringkasan aman-HTML: per paragraf, rujukan [#id] jadi link bernomor ke kartu hadist. */
    public function ringkasanHtml(): ?HtmlString
    {
        if (blank($this->ringkasan)) {
            return null;
        }

        $urutan = $this->hadist->pluck('id')->flip();
        $html = preg_replace_callback(
            '/\[#(\d+)\]/',
            fn ($m) => $urutan->has((int) $m[1])
                ? '<a href="#h-'.$m[1].'" class="font-semibold text-emerald-700 hover:underline dark:text-emerald-400">['.($urutan[(int) $m[1]] + 1).']</a>'
                : '',
            e($this->ringkasan),
        );

        return new HtmlString(collect(preg_split('/\n\s*\n/', trim($html)))
            ->map(fn ($p) => '<p>'.nl2br(trim($p)).'</p>')
            ->implode(''));
    }
}
