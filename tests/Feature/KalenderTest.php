<?php

it('menampilkan kalender Masehi dengan tanggal Hijriah', function () {
    $this->get('/kalender?bulan=2026-11')->assertOk()
        ->assertSee('November 2026')
        ->assertSee('Jumadilawal 1448 H – Jumadilakhir 1448 H')
        ->assertSee('title="22 Jumadilawal 1448 H"', false) // 2 November 2026
        ->assertSee('href="/kalender?bulan=2026-10"', false)
        ->assertSee('href="/kalender?bulan=2026-12"', false);
});

it('menyorot hari ini dan kembali ke bulan berjalan bila parameter tidak sah', function (string $bulan) {
    $this->travelTo(now('Asia/Jakarta')->setDate(2026, 3, 15));

    $this->get('/kalender?bulan='.$bulan)->assertOk()->assertSee('Maret 2026')->assertSee('aria-current="date"', false);
})->with(['', '2026-13', 'abc', '1800-01']);

it('menautkan kalender dan tentang kami di footer dan widget, bukan di header', function () {
    $halaman = $this->get('/')->assertOk()->getContent();
    $bagian = fn (string $dari, string $sampai) => str($halaman)->between($dari, $sampai)->toString();

    expect($bagian('<header', '</header>'))->not->toContain('/kalender')->not->toContain('/tentang')
        ->and($bagian('<footer', '</footer>'))->toContain('href="/kalender"')->toContain('href="/tentang"')
        ->and($bagian('data-widget-sholat', '</section>'))->toContain('href="/kalender"');
});

it('halaman tentang kami menyebut Sayogi Creative Agency', function () {
    $this->get('/tentang')->assertOk()->assertSee('Sayogi Creative Agency')->assertSee('<title>Tentang kami — Sayogi Mengaji</title>', false);
});
