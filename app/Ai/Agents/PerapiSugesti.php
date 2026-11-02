<?php

namespace App\Ai\Agents;

use App\Ai\Agents\Concerns\PakaiEffortDeepSeek;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasProviderOptions;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

/** Merapikan kata yang paling sering dicari jadi sugesti pencarian di beranda. */
#[Timeout(30)]
class PerapiSugesti implements Agent, HasProviderOptions, HasStructuredOutput
{
    use PakaiEffortDeepSeek, Promptable;

    public function instructions(): string
    {
        return <<<'TXT'
        Kamu menyusun sugesti pencarian untuk situs pencarian hadist berbahasa Indonesia.
        Masukan: daftar kata/frasa yang dicari pengunjung 30 hari terakhir, satu per baris, dengan jumlah pencariannya, urut dari yang terbanyak.
        Susun paling banyak 8 sugesti:
        - gabungkan yang semakna atau salah ketik jadi satu frasa yang rapi (mis. "hadis sabar", "hadist kesabaran" → "keutamaan sabar"),
        - frasa pendek 2–5 kata, huruf kecil, tanpa tanda baca di akhir,
        - hanya dari topik yang ada di daftar; jangan mengarang topik baru,
        - buang yang tidak pantas tampil di beranda: data pribadi, nama orang biasa, kata kasar, topik yang bukan soal agama,
        - urutkan dari yang paling banyak dicari.
        TXT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'sugesti' => $schema->array()->items($schema->string())->required(),
        ];
    }
}
