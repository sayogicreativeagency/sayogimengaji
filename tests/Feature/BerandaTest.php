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

it('header polos tanpa pola latar, dengan logo dan tagline Imam asy-Syafi\'i', function () {
    $this->get('/kontak')->assertOk()->assertDontSee('pola-islami')
        ->assertSee('src="/images/logo.webp"', false)
        ->assertSee('ليس العلم ما حفظ، إنما العلم ما نفع');
});

it('menu HP: tombol burger membuka dialog berisi menu yang sama dengan desktop', function () {
    $halaman = $this->get('/kontak')->assertOk()->getContent();
    $dialog = str($halaman)->between('<dialog id="menu-hp"', '</dialog>')->toString();

    expect($halaman)->toContain('data-buka-menu')->toContain('aria-controls="menu-hp"')
        ->and($dialog)->toContain('data-tutup-menu')
        ->toContain('href="/hadist"')->toContain('href="/belajar"')->toContain('href="/quran"')
        ->toContain('href="/dukung"')->toContain('href="/masuk"')
        ->not->toContain('href="/admin"');
});
