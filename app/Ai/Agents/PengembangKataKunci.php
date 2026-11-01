<?php

namespace App\Ai\Agents;

use App\Ai\Agents\Concerns\PakaiEffortDeepSeek;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasProviderOptions;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

/** Mengubah pertanyaan pengguna jadi kata kunci untuk FULLTEXT. */
#[Timeout(20)]
class PengembangKataKunci implements Agent, HasProviderOptions, HasStructuredOutput
{
    use PakaiEffortDeepSeek, Promptable;

    public function instructions(): string
    {
        return <<<'TXT'
        Kamu membantu mencari hadist di database berbahasa Indonesia.
        Ubah pertanyaan pengguna menjadi 5–12 kata kunci pencarian:
        - kata Indonesia yang mungkin muncul di terjemahan hadist (termasuk bentuk kata kerjanya, mis. "menikah", "nikah", "istri"),
        - istilah Arab yang biasa ditulis latin (mis. "mahar", "wali", "khitbah"),
        - 1–2 kata Inggris untuk judul bab kitab (mis. "marriage").
        Satu kata atau frasa pendek per item. Jangan sertakan kata umum seperti "hadist", "tentang", "apa".
        TXT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'kata_kunci' => $schema->array()->items($schema->string())->required(),
        ];
    }
}
