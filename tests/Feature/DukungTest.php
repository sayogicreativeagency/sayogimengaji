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
