<?php

use App\Actions\ImporHadist;
use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

function fakeDataset(array $ind, array $ara): void
{
    $sections = ['', 'The Book on Purification', 'The Book on Marriage'];

    Http::fake([
        '*/ind-abudawud.min.json' => Http::response(['metadata' => ['name' => 'x', 'sections' => $sections], 'hadiths' => $ind]),
        '*/ara-abudawud.min.json' => Http::response(['metadata' => ['name' => 'x', 'sections' => $sections], 'hadiths' => $ara]),
    ]);
}

function hadistData(int|float $nomor, string $teks, int $bab = 1, array $grades = []): array
{
    return ['hadithnumber' => $nomor, 'arabicnumber' => $nomor, 'text' => $teks, 'grades' => $grades, 'reference' => ['book' => $bab, 'hadith' => 1]];
}

beforeEach(fn () => fakeDataset(
    [
        hadistData(1, 'Telah menceritakan kepada kami tentang wudhu', 1, [['name' => 'Al-Albani', 'grade' => 'Hasan Sahih']]),
        hadistData(391.2, 'Menikahlah kalian', 2, [['name' => 'Al-Albani', 'grade' => 'Daif']]),
    ],
    [hadistData(1, 'حَدَّثَنَا'), hadistData(391.2, 'تَزَوَّجُوا')],
));

it('menggabungkan edisi indonesia dan arab', function () {
    expect((new ImporHadist)(Kitab::AbuDawud))->toBe(2);

    $hadist = Hadist::where('nomor', '391.2')->sole();

    expect($hadist)
        ->kitab->toBe(Kitab::AbuDawud)
        ->terjemahan->toBe('Menikahlah kalian')
        ->teks_arab->toBe('تَزَوَّجُوا')
        ->bab_nomor->toBe(2)
        ->bab_judul->toBe('The Book on Marriage')
        ->derajat->toBe([['ulama' => 'Al-Albani', 'derajat' => 'Daif']])
        ->derajat_ringkas->toBe(Derajat::Dhaif);
});

it('aman dijalankan ulang', function () {
    (new ImporHadist)(Kitab::AbuDawud);
    (new ImporHadist)(Kitab::AbuDawud);

    expect(Hadist::count())->toBe(2);
});

it('tidak menimpa hadist yang sudah dikoreksi admin', function () {
    (new ImporHadist)(Kitab::AbuDawud);
    Hadist::where('nomor', '1')->update(['terjemahan' => 'Koreksi admin', 'dikoreksi_at' => now()]);

    expect((new ImporHadist)(Kitab::AbuDawud))->toBe(1)
        ->and(Hadist::where('nomor', '1')->value('terjemahan'))->toBe('Koreksi admin');
});

it('melempar exception bila unduhan gagal', function () {
    Http::fake(['*/ind-malik.min.json' => Http::response(status: 500)]);

    (new ImporHadist)(Kitab::Malik);
})->throws(RequestException::class);

it('command mengimpor kitab yang dipilih', function () {
    $this->artisan('hadist:impor', ['kitab' => 'abudawud'])->assertSuccessful();

    expect(Hadist::count())->toBe(2);
});

it('command menolak kitab yang tidak dikenal', function () {
    $this->artisan('hadist:impor', ['kitab' => 'tidakada'])->assertFailed();
});
