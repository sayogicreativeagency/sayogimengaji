<?php

it('menampilkan halaman dukung dengan tombol ke saweria', function () {
    config(['services.saweria.url' => 'https://saweria.co/hadistapa']);

    $this->get('/dukung')->assertOk()
        ->assertSee('Infaq untuk Hadist Apa')
        ->assertSee('href="https://saweria.co/hadistapa"', false)
        ->assertSee('bukan penggalangan dana untuk pihak lain')
        ->assertSee('href="'.route('hadist', ['muslim', '6592']).'"', false)
        ->assertSee('href="'.route('hadist', ['muslim', '4223']).'"', false);
});

it('menyembunyikan tombol bila akun saweria belum diatur', function () {
    config(['services.saweria.url' => null]);

    $this->get('/dukung')->assertOk()
        ->assertSee('Segera hadir')
        ->assertDontSee('saweria.co', false);
});

it('menautkan halaman dukung dari header dan footer', function () {
    $this->get('/')->assertOk()->assertSee('href="/dukung"', false);
});

it('menampilkan papan donasi di sidebar halaman publik', function (string $url) {
    config(['services.saweria.url' => 'https://saweria.co/hadistapa', 'services.saweria.qris' => 'img/qris-saweria.png']);

    $this->get($url)->assertOk()
        ->assertSee('<aside', false)
        ->assertSee('Bantu Hadist Apa tetap hidup')
        ->assertSee('src="'.asset('img/qris-saweria.png').'"', false)
        ->assertSee('href="https://saweria.co/hadistapa"', false)
        ->assertSee('href="/dukung"', false);
})->with(['/', '/kontak']);

it('tanpa sidebar di halaman dukung karena isinya sudah lengkap', function () {
    $this->get('/dukung')->assertOk()->assertDontSee('<aside', false);
});

it('menampilkan QRIS di halaman dukung bila diatur', function () {
    config(['services.saweria.url' => 'https://saweria.co/hadistapa', 'services.saweria.qris' => 'img/qris-saweria.png']);

    $this->get('/dukung')->assertOk()->assertSee('src="'.asset('img/qris-saweria.png').'"', false);
});

it('tanpa gambar QRIS bila belum diatur', function () {
    config(['services.saweria.url' => 'https://saweria.co/hadistapa', 'services.saweria.qris' => null]);

    $this->get('/')->assertOk()->assertDontSee('alt="QRIS', false);
});
