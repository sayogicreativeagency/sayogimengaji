<?php

use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;
use App\Models\Surat;
use App\Support\LiburNasional;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Surat::create(['nomor' => 36, 'nama' => 'يٰسۤ', 'nama_latin' => 'Yasin', 'arti' => 'Yasin', 'tempat_turun' => 'Mekah', 'jumlah_ayat' => 83, 'deskripsi' => '-']);
    $this->hadist = Hadist::factory()->create([
        'kitab' => Kitab::Muslim, 'nomor' => '6592', 'derajat_ringkas' => Derajat::Shahih,
        'terjemahan' => 'Telah menceritakan kepada kami [Yahya] dari [Abu Hurairah] dari Rasulullah bersabda: "Sedekah itu tidak akan mengurangi harta."',
    ]);
});

it('beranda punya judul, deskripsi, canonical, Open Graph, dan JSON-LD situs', function () {
    $this->get('/')->assertOk()
        ->assertSee('<title>Cari Hadist, Murottal &amp; Belajar Al-Qur&#039;an — Sayogi Mengaji</title>', false)
        ->assertSee('<meta name="description" content="Cari lebih dari 36 ribu hadist', false)
        ->assertSee('<link rel="canonical" href="'.url('/').'">', false)
        ->assertSee('<meta property="og:image" content="'.url('/images/og.jpg').'">', false)
        ->assertSee('"@type":"SearchAction","target":"'.url('/cari').'?q={search_term_string}"', false)
        ->assertSee('"name":"Sayogi Creative Agency"', false);
});

it('halaman surat dan hadist punya judul dan deskripsi yang bisa dicari', function () {
    $this->get('/quran/36')->assertOk()
        ->assertSee('<title>Surat Yasin: Arab, Latin, Terjemahan &amp; Murottal — Sayogi Mengaji</title>', false)
        ->assertSee('Baca Surat Yasin (يٰسۤ, artinya Yasin), 83 ayat');

    // Deskripsi memakai matan (sesudah sanad), bukan nama perawi.
    $this->get('/hadist/muslim/6592')->assertOk()
        ->assertSee('<meta name="description" content="Shahih Muslim no. 6592 (Shahih): Sedekah itu tidak akan mengurangi harta">', false);
});

it('kalender per bulan punya canonical bulannya sendiri', function () {
    Http::fake([LiburNasional::URL => Http::response('')]);

    $this->get('/kalender?bulan=2026-12')->assertOk()
        ->assertSee('<link rel="canonical" href="'.url('/kalender?bulan=2026-12').'">', false)
        ->assertSee('<title>Kalender Desember 2026 Masehi &amp; Hijriah', false);
});

it('hasil cari dan halaman akun tidak diindeks', function (string $url) {
    $this->get($url)->assertOk()->assertSee('<meta name="robots" content="noindex, follow">', false)->assertDontSee('rel="canonical"', false);
})->with(['/masuk', '/daftar']);

it('robots.txt menutup admin dan menunjuk ke sitemap', function () {
    $this->get('/robots.txt')->assertOk()->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Disallow: /admin')
        ->assertSee('Sitemap: '.url('/sitemap.xml'));
});

it('sitemap berisi halaman utama, surat, dan hadist, tanpa tentang kami dan hasil cari', function () {
    $isi = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')->getContent();
    $alamat = collect();
    foreach (simplexml_load_string($isi)->url as $u) { // collect() langsung menimpa elemen berkunci sama ("url")
        $alamat->push((string) $u->loc);
    }

    expect($alamat)->toContain(url('/'), url('/hadist'), url('/quran/36'), url('/hadist/muslim/6592'))
        ->and($alamat->filter(fn ($a) => str_contains($a, '/tentang') || str_contains($a, '/cari')))->toBeEmpty();
});
