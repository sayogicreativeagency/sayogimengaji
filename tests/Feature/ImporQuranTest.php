<?php

use App\Actions\ImporQuran;
use App\Models\Ayat;
use App\Models\Surat;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

function suratEquran(int $nomor, int $jumlahAyat, string $deskripsi = 'Surat <i>Al Faatihah</i>.<br> Dibaca tiap sholat.'): array
{
    return ['code' => 200, 'data' => [
        'nomor' => $nomor, 'nama' => 'الفاتحة', 'namaLatin' => 'Al-Fatihah', 'jumlahAyat' => $jumlahAyat,
        'tempatTurun' => 'Mekah', 'arti' => 'Pembukaan', 'deskripsi' => $deskripsi, 'audioFull' => [],
        'ayat' => array_map(fn ($n) => [
            'nomorAyat' => $n, 'teksArab' => "عربي {$n}", 'teksLatin' => "latin {$n} ", 'teksIndonesia' => "arti {$n}", 'audio' => [],
        ], range(1, $jumlahAyat)),
    ]];
}

it('mengimpor surat beserta ayatnya', function () {
    Http::fake(['equran.id/api/v2/surat/1' => Http::response(suratEquran(1, 7))]);

    expect((new ImporQuran)(1))->toBe(7);

    expect(Surat::find(1))
        ->nama_latin->toBe('Al-Fatihah')->arti->toBe('Pembukaan')->tempat_turun->toBe('Mekah')->jumlah_ayat->toBe(7)
        ->and(Ayat::where('surat_nomor', 1)->where('nomor', 3)->sole())
        ->teks_arab->toBe('عربي 3')->teks_latin->toBe('latin 3')->terjemahan->toBe('arti 3');
});

it('aman dijalankan ulang', function () {
    Http::fake(['equran.id/api/v2/surat/1' => Http::response(suratEquran(1, 7))]);

    (new ImporQuran)(1);
    (new ImporQuran)(1);

    expect(Surat::count())->toBe(1)->and(Ayat::count())->toBe(7);
});

it('membersihkan HTML deskripsi kecuali miring dan baris baru', function () {
    Http::fake(['equran.id/api/v2/surat/1' => Http::response(suratEquran(1, 1, '<i onclick="x()">Al</i><br/><script>alert(1)</script><b>tebal</b>'))]);

    (new ImporQuran)(1);

    expect(Surat::find(1)->deskripsi)->toBe('<i>Al</i><br>alert(1)tebal');
});

it('melempar exception bila unduhan gagal', function () {
    Http::fake(['*' => Http::response(status: 500)]);

    (new ImporQuran)(1);
})->throws(RequestException::class);

it('command mengimpor surat yang dipilih', function () {
    Http::fake(['equran.id/api/v2/surat/2' => Http::response(suratEquran(2, 3))]);

    $this->artisan('quran:impor', ['surat' => 2])->assertSuccessful();

    expect(Ayat::where('surat_nomor', 2)->count())->toBe(3);
});

it('command menolak nomor surat di luar 1-114', function () {
    $this->artisan('quran:impor', ['surat' => 115])->assertFailed();
});
