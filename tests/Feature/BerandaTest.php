<?php

use App\Models\Surat;

it('beranda: hero, section Al-Qur\'an + widget, cari hadist, lalu section bantu di atas footer', function () {
    Surat::create(['nomor' => 9, 'nama' => 'التوبة', 'nama_latin' => 'At-Taubah', 'arti' => 'Pengampunan', 'tempat_turun' => 'Madinah', 'jumlah_ayat' => 129, 'deskripsi' => '-']);
    Surat::create(['nomor' => 36, 'nama' => 'يٰسۤ', 'nama_latin' => 'Yasin', 'arti' => 'Yasin', 'tempat_turun' => 'Mekah', 'jumlah_ayat' => 83, 'deskripsi' => '-']);

    $this->get('/')->assertOk()
        ->assertSeeInOrder([
            '<h1', 'href="/quran"', 'href="#cari"',
            'id="quran"', 'id="cari-surat"', 'data-mini-murottal', 'data-widget-sholat',
            'id="cari"', 'action="/cari"',
            'Bantu Sayogi Mengaji tetap hidup', '<footer',
        ], false)
        ->assertSee('src="/images/hero.webp"', false)
        // Pilihan surat membawa data untuk mini player; At-Taubah tanpa basmalah.
        ->assertSeeInOrder(['data-nomor="9"', 'data-basmalah="0"', 'data-nomor="36"', 'data-jumlah="83"', 'data-basmalah="1"'], false);
});

it('header polos tanpa pola latar, dengan logo dan tagline Imam asy-Syafi\'i', function () {
    $this->get('/kontak')->assertOk()->assertDontSee('pola-islami')
        ->assertSee('src="/images/logo.webp"', false)
        ->assertSee('ليس العلم ما حفظ، إنما العلم ما نفع');
});

it('footer: tombol bagikan ke WhatsApp membawa hadist dan alamat halaman, kutipannya tertaut ke sumber', function () {
    $pesan = rawurlencode("“Sampaikanlah dariku walau hanya satu ayat.” (HR. Bukhari)\n\n".url('/kontak?dari=footer'));

    $this->get('/kontak?dari=footer')->assertOk()
        ->assertSee('href="https://wa.me/?text='.$pesan.'"', false)
        ->assertSee('href="/hadist/bukhari/3461"', false);
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
