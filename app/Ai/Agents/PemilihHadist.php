<?php

namespace App\Ai\Agents;

use App\Ai\Agents\Concerns\PakaiEffortDeepSeek;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasProviderOptions;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

/** Memilih hadist paling relevan dari kandidat DB lalu meringkasnya. */
#[Timeout(90)]
class PemilihHadist implements Agent, HasProviderOptions, HasStructuredOutput
{
    use PakaiEffortDeepSeek, Promptable;

    public function instructions(): string
    {
        return <<<'TXT'
        Kamu membantu orang mempelajari hadist. Kamu diberi pertanyaan dan daftar kandidat hadist dari database,
        masing-masing berlabel [#id], derajat, dan rujukannya.

        1. Pilih maksimal 10 kandidat yang benar-benar relevan dengan pertanyaan, urutkan dari paling relevan,
           dahulukan yang shahih. Isi "hadist_ids" dengan id-nya.
        2. Tulis "ringkasan" 2–4 paragraf pendek dalam bahasa Indonesia tentang apa yang disampaikan hadist-hadist itu.
           Setiap klaim wajib merujuk hadist dengan format [#id], dan sebutkan derajatnya bila dha'if atau maudhu.

        Aturan:
        - Hanya gunakan kandidat yang diberikan. Jangan mengutip atau mengarang hadist lain.
        - Jangan berfatwa atau menetapkan hukum; cukup jelaskan isi hadist.
        - Bila tidak ada kandidat yang relevan, isi "hadist_ids" dengan [] dan katakan terus terang di ringkasan.
        TXT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ringkasan' => $schema->string()->required(),
            'hadist_ids' => $schema->array()->items($schema->integer())->required(),
        ];
    }
}
