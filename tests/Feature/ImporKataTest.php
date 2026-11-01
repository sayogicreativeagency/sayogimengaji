<?php

use App\Actions\ImporKata;
use App\Enums\QoriBelajar;
use App\Models\Kata;
use App\Models\WaktuKata;
use Illuminate\Support\Facades\Http;

function ayatQuranCom(string $kunci, array $kata, array $segmen): array
{
    $words = array_map(fn ($teks, $i) => ['position' => $i + 1, 'char_type_name' => 'word', 'text_uthmani' => $teks], $kata, array_keys($kata));
    $words[] = ['position' => count($kata) + 1, 'char_type_name' => 'end', 'text_uthmani' => '١'];

    return ['verse_key' => $kunci, 'words' => $words, 'audio' => ['url' => 'x.mp3', 'segments' => $segmen]];
}

beforeEach(fn () => Http::fake(['api.quran.com/api/v4/verses/by_chapter/112*' => Http::response(['verses' => [
    ayatQuranCom('112:1', ['قُلْ', 'هُوَ', 'ٱللَّهُ', 'أَحَدٌ'], [[0, 1, 30, 390], [1, 2, 400, 790], [2, 3, 800, 1640], [3, 4, 1650, 2300]]),
    // Format 3 elemen dan segmen untuk posisi yang tidak ada ikut diuji.
    ayatQuranCom('112:2', ['ٱللَّهُ', 'ٱلصَّمَدُ'], [[1, 0, 700], [2, 710, 1900], [9, 2000, 2100]]),
]])]));

it('mengimpor kata utsmani dan waktunya per qori', function () {
    expect((new ImporKata)(112, QoriBelajar::Afasi))->toBe(6);

    expect(Kata::where('surat_nomor', 112)->where('ayat_nomor', 1)->orderBy('posisi')->pluck('teks')->all())
        ->toBe(['قُلْ', 'هُوَ', 'ٱللَّهُ', 'أَحَدٌ'])
        ->and(WaktuKata::where(['qori' => 'afasi', 'surat_nomor' => 112, 'ayat_nomor' => 1, 'posisi' => 3])->sole())
        ->mulai_ms->toBe(800)->selesai_ms->toBe(1640)
        ->and(WaktuKata::where(['surat_nomor' => 112, 'ayat_nomor' => 2])->orderBy('posisi')->pluck('mulai_ms')->all())->toBe([0, 710]);

    Http::assertSent(fn ($r) => str_contains($r->url(), 'audio=7') && str_contains($r->url(), 'words=true'));
});

it('aman dijalankan ulang dan untuk qori lain', function () {
    (new ImporKata)(112, QoriBelajar::Afasi);
    (new ImporKata)(112, QoriBelajar::Afasi);
    (new ImporKata)(112, QoriBelajar::HusaryMuallim);

    expect(Kata::count())->toBe(6)->and(WaktuKata::count())->toBe(12);
});

it('command mengimpor surat yang dipilih untuk semua qori', function () {
    $this->artisan('quran:impor-kata', ['surat' => 112])->assertSuccessful();

    expect(WaktuKata::distinct()->pluck('qori')->sort()->values()->all())->toBe(['afasi', 'husary-muallim']);
});
