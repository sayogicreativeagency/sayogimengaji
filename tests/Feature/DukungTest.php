<?php

use App\Support\Qris;

it('menampilkan halaman dukung dengan tombol ke saweria', function () {
    config(['services.saweria.url' => 'https://saweria.co/hadistapa']);

    $this->get('/dukung')->assertOk()
        ->assertSee('Infaq untuk Sayogi Mengaji')
        ->assertSee('href="https://saweria.co/hadistapa"', false)
        ->assertSee('bukan penggalangan dana untuk pihak lain')
        ->assertSee('href="'.route('hadist', ['muslim', '6592']).'"', false)
        ->assertSee('href="'.route('hadist', ['muslim', '4223']).'"', false);
});

it('menyembunyikan tombol dan QR bila akun saweria belum diatur', function () {
    config(['services.saweria.url' => null]);

    $this->get('/dukung')->assertOk()
        ->assertSee('Segera hadir')
        ->assertDontSee('saweria.co', false)
        ->assertDontSee('data:image/svg+xml', false);
});

it('menautkan halaman dukung dari header dan footer', function () {
    $this->get('/')->assertOk()->assertSee('href="/dukung"', false);
});

it('menampilkan papan donasi dengan QR otomatis di sidebar halaman publik', function (string $url) {
    config(['services.saweria.url' => 'https://saweria.co/hadistapa']);

    $this->get($url)->assertOk()
        ->assertSee('<aside', false)
        ->assertSee('Bantu Sayogi Mengaji tetap hidup')
        ->assertSee('src="data:image/svg+xml;base64,', false)
        ->assertSee('alt="Kode QR ke halaman Saweria Sayogi Mengaji"', false)
        ->assertSee('href="https://saweria.co/hadistapa"', false)
        ->assertSee('href="/dukung"', false);
})->with(['/', '/kontak']);

it('menampilkan QR otomatis di halaman dukung', function () {
    config(['services.saweria.url' => 'https://saweria.co/hadistapa']);

    $this->get('/dukung')->assertOk()->assertSee('src="data:image/svg+xml;base64,', false);
});

it('tanpa sidebar di halaman dukung karena isinya sudah lengkap', function () {
    $this->get('/dukung')->assertOk()->assertDontSee('<aside', false);
});

it('menampilkan QRIS infaq beserta nama penerima bila diatur', function () {
    $nama = '5910HADIST APA';
    config([
        'services.saweria.url' => 'https://saweria.co/hadistapa',
        'services.infaq.qris' => Qris::denganCrc('000201010211520448295303360'.'5802ID'.$nama.'6007JAKARTA6304'),
    ]);

    $this->get('/')->assertOk()
        ->assertSee('alt="QRIS infaq Sayogi Mengaji atas nama HADIST APA"', false)
        ->assertSee('atas nama <strong>HADIST APA</strong>', false)
        ->assertDontSee('alt="Kode QR ke halaman Saweria', false)
        ->assertSee('href="https://saweria.co/hadistapa"', false);
});

it('kembali ke QR Saweria bila QRIS rusak', function () {
    config(['services.saweria.url' => 'https://saweria.co/hadistapa', 'services.infaq.qris' => '000201xxxx6304ABCD']);

    $this->get('/')->assertOk()->assertDontSee('alt="QRIS infaq', false)->assertSee('alt="Kode QR ke halaman Saweria Sayogi Mengaji"', false);
});

it('memberi jalan lain bila aplikasi bank menolak QRIS', function () {
    config(['services.infaq.qris' => Qris::denganCrc('000201010211520448295303360'.'5802ID'.'5910HADIST APA'.'6007JAKARTA6304')]);

    $this->get('/dukung')->assertOk()->assertSee('Kalau aplikasi bankmu menolak, coba dari e-wallet atau lewat tombol Saweria.');
});
