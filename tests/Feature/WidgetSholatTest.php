<?php

it('menampilkan widget jam sholat di atas papan donasi', function (string $url) {
    $this->get($url)->assertOk()
        ->assertSeeInOrder(['<aside', 'data-widget-sholat', 'Bantu Hadist Apa tetap hidup'], false)
        ->assertSeeInOrder(['Imsak', 'Subuh', 'Terbit', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'])
        ->assertSee('data-minta-lokasi', false)
        ->assertSee('<option value="hanafi">Hanafi</option>', false);
})->with(['/', '/hadist', '/quran', '/kontak']);

it('menyediakan halaman cari hadist di /hadist dan menu Hadist', function () {
    $this->get('/hadist')->assertOk()->assertSee('name="q"', false);
    $this->get('/')->assertOk()->assertSeeInOrder(['href="/hadist"', 'href="/quran"', 'href="/dukung"'], false);
});
