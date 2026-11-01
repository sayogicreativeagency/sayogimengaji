<?php

use App\Ai\Agents\PemilihHadist;
use App\Ai\Agents\PengembangKataKunci;
use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;

it('menampilkan beranda dengan kotak pencarian', function () {
    $this->get('/')->assertOk()->assertSee('name="q"', false);
});

it('mengarahkan pencarian kosong ke beranda', function () {
    $this->get('/cari?q=%20')->assertRedirect('/');
});

it('menampilkan ringkasan dan kartu hadist', function () {
    $hadist = Hadist::factory()->create([
        'kitab' => Kitab::Bukhari, 'nomor' => '5063', 'terjemahan' => 'Siapa yang mampu menikah maka menikahlah',
        'teks_arab' => 'مَنِ اسْتَطَاعَ', 'derajat_ringkas' => Derajat::Shahih,
    ]);
    PengembangKataKunci::fake([['kata_kunci' => ['menikah']]]);
    PemilihHadist::fake([['ringkasan' => "Nabi menganjurkan menikah <b>[#{$hadist->id}]</b>.", 'hadist_ids' => [$hadist->id]]]);

    $this->get('/cari?q=pernikahan&derajat=shahih&kitab=bukhari')
        ->assertOk()
        ->assertSee('Nabi menganjurkan menikah')
        ->assertSee('&lt;b&gt;', false)
        ->assertSee('href="#h-'.$hadist->id.'"', false)
        ->assertSee('مَنِ اسْتَطَاعَ')
        ->assertSee('HR. Bukhari no. 5063')
        ->assertSee('Shahih')
        ->assertSee('Ringkasan ini dibuat oleh AI')
        ->assertSee('patokan utamanya adalah teks hadist')
        ->assertSee('href="'.url('/kontak?q=pernikahan').'"', false);
});

it('memberi tahu bila ringkasan AI tidak tersedia', function () {
    Hadist::factory()->create(['terjemahan' => 'menikahlah']);
    PengembangKataKunci::fake(fn () => throw new RuntimeException('x'));
    PemilihHadist::fake(fn () => throw new RuntimeException('x'));

    $this->get('/cari?q=menikahlah')->assertOk()->assertSee('Ringkasan AI sedang tidak tersedia')->assertSee('menikahlah');
});

it('mengabaikan filter yang tidak dikenal', function () {
    PengembangKataKunci::fake([['kata_kunci' => ['zakat']]]);

    $this->get('/cari?q=zakat&derajat=ngawur&kitab=ngawur')->assertOk()->assertSee('Tidak ditemukan');
});

it('menampilkan detail hadist dengan semua penilaian ulama', function () {
    Hadist::factory()->create([
        'kitab' => Kitab::AbuDawud, 'nomor' => '391.2', 'bab_judul' => 'The Book on Marriage',
        'derajat' => [['ulama' => 'Al-Albani', 'derajat' => 'Hasan Sahih'], ['ulama' => 'Zubair Ali Zai', 'derajat' => 'Isnaad Hasan']],
    ]);

    $this->get('/hadist/abudawud/391.2')->assertOk()
        ->assertSee('HR. Abu Dawud no. 391.2')->assertSee('The Book on Marriage')
        ->assertSee('Al-Albani')->assertSee('Isnaad Hasan');
});

it('404 untuk hadist atau kitab yang tidak ada', function () {
    $this->get('/hadist/abudawud/1')->assertNotFound();
    $this->get('/hadist/tidakada/1')->assertNotFound();
});

it('menampilkan daftar isi per kitab untuk loncat ke hadist', function () {
    $muslim = Hadist::factory()->create(['kitab' => Kitab::Muslim, 'nomor' => '1400', 'terjemahan' => 'menikahlah']);
    $bukhari = Hadist::factory()->create(['kitab' => Kitab::Bukhari, 'nomor' => '5063', 'terjemahan' => 'menikahlah']);
    $muslim2 = Hadist::factory()->create(['kitab' => Kitab::Muslim, 'nomor' => '1401', 'terjemahan' => 'menikahlah']);
    PengembangKataKunci::fake([['kata_kunci' => ['menikahlah']]]);
    PemilihHadist::fake([['ringkasan' => 'x', 'hadist_ids' => [$muslim->id, $bukhari->id, $muslim2->id]]]);

    $this->get('/cari?q=menikah')->assertOk()
        ->assertSeeInOrder(['Loncat ke', 'HR. Bukhari', 'no. 5063', 'HR. Muslim', 'no. 1400', 'no. 1401'])
        ->assertSee('href="#h-'.$muslim2->id.'"', false);
});

it('menyiapkan filter tanpa reload hanya untuk kitab dan derajat yang ada di hasil', function () {
    $a = Hadist::factory()->create(['kitab' => Kitab::Muslim, 'derajat_ringkas' => Derajat::Shahih, 'terjemahan' => 'menikahlah']);
    $b = Hadist::factory()->create(['kitab' => Kitab::AbuDawud, 'derajat_ringkas' => Derajat::Dhaif, 'terjemahan' => 'menikahlah']);
    $c = Hadist::factory()->create(['kitab' => Kitab::AbuDawud, 'derajat_ringkas' => null, 'terjemahan' => 'menikahlah']);
    PengembangKataKunci::fake([['kata_kunci' => ['menikahlah']]]);
    PemilihHadist::fake([['ringkasan' => 'x', 'hadist_ids' => [$a->id, $b->id, $c->id]]]);

    $this->get('/cari?q=menikah')->assertOk()
        ->assertSee('data-filter-hasil', false)
        ->assertDontSee('<select id="derajat" name="derajat"', false)
        ->assertSee('<option value="muslim">HR. Muslim (1)</option>', false)
        ->assertSee('<option value="abudawud">HR. Abu Dawud (2)</option>', false)
        ->assertDontSee('<option value="bukhari">', false)
        ->assertSee('<option value="dhaif">Dha&#039;if (1)</option>', false)
        ->assertSee('<option value="belum">Belum dinilai (1)</option>', false)
        ->assertDontSee('<option value="hasan">', false)
        ->assertSee('data-hadist data-kitab="abudawud" data-derajat="belum"', false);
});
