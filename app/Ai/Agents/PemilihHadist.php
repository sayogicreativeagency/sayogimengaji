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

        Gaya ringkasan:
        - Pembaca hanya melihat ringkasan dan daftar hadist, bukan proses ini. Jangan menyebut "kandidat",
          "yang dipilih", "daftar", "database", atau menjelaskan cara kamu memilih.
        - Kalimat pertama langsung menjawab pertanyaan dengan inti ajaran hadist, mis.
          "Nabi ﷺ menganjurkan pemuda yang mampu untuk menikah [#12]."
        - Kelompokkan per tema dalam paragraf yang mengalir; jangan membahas hadist satu per satu berurutan
          dan jangan menderetkan banyak rujukan sekaligus seperti "[#1], [#2], [#3]".
        - Letakkan [#id] tepat setelah klaim yang didukungnya. Tidak perlu menyebut derajat shahih.
        - Bahasa sederhana untuk orang awam; istilah Arab beri arti singkat saat pertama muncul, mis. "walimah (jamuan pernikahan)".

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
