<?php

use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;

it('/hadist: form cari, penjelasan derajat, dan daftar kitab dengan jumlah hadistnya', function () {
    Hadist::factory()->count(3)->create(['kitab' => Kitab::Bukhari, 'derajat_ringkas' => Derajat::Shahih]);
    Hadist::factory()->count(2)->create(['kitab' => Kitab::Tirmidhi, 'derajat_ringkas' => Derajat::Hasan]);
    Hadist::factory()->create(['kitab' => Kitab::Tirmidhi, 'derajat_ringkas' => null]);

    $this->get('/hadist')->assertOk()
        ->assertSee('action="/cari"', false)
        ->assertSee('Kami mencari di 6 hadist dari 7 kitab')
        ->assertSeeInOrder(['Memahami derajat hadist', 'Shahih', '3 hadist', 'Hasan', '2 hadist', "Dha'if", '0 hadist', 'Maudhu', 'Belum dinilai', '1 hadist'])
        ->assertSee('al-Albani')
        ->assertSeeInOrder(['Kitab yang dipakai', 'Shahih Bukhari', 'Imam al-Bukhari (w. 256 H)', '3', "Muwaththa' Malik", 'Jumlah', '6']);
});

it('beranda dan /hadist kini halaman yang berbeda', function () {
    $this->get('/')->assertOk()->assertSee("Mengaji hadist dan Al-Qur'an", false)->assertDontSee('Memahami derajat hadist');
    $this->get('/hadist')->assertOk()->assertSee('<title>Cari hadist — Sayogi Mengaji</title>', false)->assertDontSee("Mengaji hadist dan Al-Qur'an", false);
});
