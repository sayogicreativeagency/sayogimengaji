<?php

it('beranda: hero dengan tombol ke form cari, lalu form + widget, lalu section bantu di atas footer', function () {
    $this->get('/')->assertOk()
        ->assertSeeInOrder([
            '<h1', 'Mengaji hadist dan Al-Qur&#039;an, kapan saja', 'href="#cari"',
            'id="cari"', 'action="/cari"', 'data-widget-sholat',
            'Bantu Sayogi Mengaji tetap hidup', '<footer',
        ], false)
        ->assertSee('src="/images/hero.webp"', false);
});

it('header polos tanpa pola latar', function () {
    $this->get('/kontak')->assertOk()->assertDontSee('pola-islami');
});
