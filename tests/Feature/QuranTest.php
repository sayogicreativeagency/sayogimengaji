<?php

use App\Enums\Qori;
use App\Models\Ayat;
use App\Models\Surat;

function buatSurat(int $nomor, int $jumlahAyat, array $atribut = []): Surat
{
    $surat = Surat::create([
        'nomor' => $nomor, 'nama' => 'سورة', 'nama_latin' => "Surat {$nomor}", 'arti' => "Arti {$nomor}",
        'tempat_turun' => 'Mekah', 'jumlah_ayat' => $jumlahAyat, 'deskripsi' => 'Tentang <i>surat</i> ini.', ...$atribut,
    ]);
    foreach (range(1, $jumlahAyat) as $n) {
        Ayat::create(['surat_nomor' => $nomor, 'nomor' => $n, 'teks_arab' => "عربي {$n}", 'teks_latin' => "latin {$n}", 'terjemahan' => "arti ayat {$n}"]);
    }

    return $surat;
}

it('menampilkan daftar surat dengan penyaring', function () {
    buatSurat(1, 7, ['nama_latin' => 'Al-Fatihah', 'arti' => 'Pembukaan']);
    buatSurat(2, 3, ['nama_latin' => 'Al-Baqarah', 'arti' => 'Sapi Betina']);

    $this->get('/quran')->assertOk()
        ->assertSee('data-saring-surat', false)
        ->assertSeeInOrder(['Al-Fatihah', 'Pembukaan', '7 ayat', 'Al-Baqarah', 'Sapi Betina'])
        ->assertSee('href="'.url('/quran/2').'"', false)
        ->assertSee('data-cari="2 al-baqarah sapi betina"', false)
        ->assertSee('data-lanjutkan', false);
});

it('menampilkan ayat, basmalah, dan pemutar di halaman surat', function () {
    buatSurat(2, 3, ['nama_latin' => 'Al-Baqarah']);
    buatSurat(3, 1);

    $this->get('/quran/2')->assertOk()
        ->assertSee('Al-Baqarah')
        ->assertSee('بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ')
        ->assertSeeInOrder(['id="ayat-1"', 'عربي 1', 'latin 1', 'arti ayat 1', 'id="ayat-3"'], false)
        ->assertSee('data-murottal', false)
        ->assertSee('data-surat="2"', false)
        ->assertSee('data-jumlah="3"', false)
        ->assertSee('data-basmalah="1"', false)
        ->assertSee(e(Qori::Afasi->urlAudio()), false)
        ->assertSee('Tentang <i>surat</i> ini.', false)
        ->assertSee('href="'.url('/quran/3').'"', false);
});

it('tanpa basmalah di Al-Fatihah dan At-Taubah', function (int $nomor) {
    buatSurat($nomor, 1);

    $this->get("/quran/{$nomor}")->assertOk()
        ->assertDontSee('بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ')
        ->assertSee('data-basmalah="0"', false);
})->with([1, 9]);

it('404 untuk surat yang tidak ada', function () {
    $this->get('/quran/115')->assertNotFound();
    $this->get('/quran/abc')->assertNotFound();
});

it('menautkan Al-Qur\'an dari header dan kredit equran di halaman sumber', function () {
    $this->get('/')->assertOk()->assertSee('href="/quran"', false)->assertSee('href="/sumber"', false);
    $this->get('/sumber')->assertOk()->assertSee('equran.id');
});
