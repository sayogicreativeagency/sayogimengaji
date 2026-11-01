<?php

use App\Actions\CariHadist;
use App\Ai\Agents\PemilihHadist;
use App\Ai\Agents\PengembangKataKunci;
use App\Enums\Derajat;
use App\Enums\Kitab;
use App\Models\Hadist;

beforeEach(function () {
    $this->nikah = Hadist::factory()->create(['terjemahan' => 'Wahai para pemuda, siapa yang mampu menikah maka menikahlah', 'bab_judul' => 'The Book on Marriage']);
    $this->mahar = Hadist::factory()->create(['terjemahan' => 'Berikanlah mahar kepada istri walau cincin besi', 'derajat_ringkas' => Derajat::Dhaif]);
    $this->wudhu = Hadist::factory()->create(['terjemahan' => 'Tidak diterima shalat tanpa wudhu', 'kitab' => Kitab::Tirmidhi]);
});

it('mengembalikan ringkasan dan hadist pilihan AI', function () {
    PengembangKataKunci::fake([['kata_kunci' => ['menikah', 'mahar']]]);
    PemilihHadist::fake(fn () => ['ringkasan' => "Anjuran menikah [#{$this->nikah->id}].", 'hadist_ids' => [$this->nikah->id, $this->mahar->id]]);

    $hasil = (new CariHadist)('hadist tentang pernikahan');

    expect($hasil->aiTersedia)->toBeTrue()
        ->and($hasil->ringkasan)->toBe("Anjuran menikah [#{$this->nikah->id}].")
        ->and($hasil->hadist->modelKeys())->toBe([$this->nikah->id, $this->mahar->id]);
});

it('membuang id dan rujukan yang bukan kandidat', function () {
    PengembangKataKunci::fake([['kata_kunci' => ['menikah']]]);
    PemilihHadist::fake([['ringkasan' => "Lihat [#{$this->nikah->id}] dan [#99999] serta [#{$this->wudhu->id}].", 'hadist_ids' => [99999, $this->wudhu->id, $this->nikah->id]]]);

    $hasil = (new CariHadist)('menikah');

    expect($hasil->hadist->modelKeys())->toBe([$this->nikah->id])
        ->and($hasil->ringkasan)->toBe("Lihat [#{$this->nikah->id}] dan  serta .");
});

it('menerapkan filter derajat dan kitab', function () {
    PengembangKataKunci::fake([['kata_kunci' => ['menikah', 'mahar', 'wudhu']], ['kata_kunci' => ['menikah', 'mahar', 'wudhu']]]);
    PemilihHadist::fake([['ringkasan' => '', 'hadist_ids' => []], ['ringkasan' => '', 'hadist_ids' => []]]);

    (new CariHadist)('apa saja', derajat: Derajat::Dhaif);
    (new CariHadist)('apa saja', kitab: Kitab::Tirmidhi);

    PemilihHadist::assertPrompted(fn ($p) => $p->contains("[#{$this->mahar->id}]") && ! $p->contains("[#{$this->nikah->id}]") && ! $p->contains("[#{$this->wudhu->id}]"));
    PemilihHadist::assertPrompted(fn ($p) => $p->contains("[#{$this->wudhu->id}]") && ! $p->contains("[#{$this->mahar->id}]"));
});

it('tetap mencari dengan kata dari query bila AI kata kunci gagal', function () {
    PengembangKataKunci::fake(fn () => throw new RuntimeException('timeout'));
    PemilihHadist::fake([['ringkasan' => 'ok', 'hadist_ids' => [$this->wudhu->id]]]);

    expect((new CariHadist)('wudhu')->hadist->modelKeys())->toBe([$this->wudhu->id]);
});

it('tanpa ringkasan dan tanpa cache bila AI pemilih gagal', function () {
    PengembangKataKunci::fake([['kata_kunci' => ['menikah']], ['kata_kunci' => ['menikah']]]);
    PemilihHadist::fake(fn () => throw new RuntimeException('kuota habis'));

    $hasil = (new CariHadist)('menikah');
    (new CariHadist)('menikah');

    expect($hasil->aiTersedia)->toBeFalse()
        ->and($hasil->ringkasan)->toBeNull()
        ->and($hasil->hadist->modelKeys())->toBe([$this->nikah->id]);
    PengembangKataKunci::assertPromptedTimes(2);
});

it('memakai cache untuk query yang sama', function () {
    PengembangKataKunci::fake([['kata_kunci' => ['menikah']]]);
    PemilihHadist::fake([['ringkasan' => 'ok', 'hadist_ids' => [$this->nikah->id]]]);

    (new CariHadist)('Hadist tentang  Menikah');
    $hasil = (new CariHadist)('hadist tentang menikah');

    expect($hasil->hadist->modelKeys())->toBe([$this->nikah->id]);
    PengembangKataKunci::assertPromptedTimes(1);
    PemilihHadist::assertPromptedTimes(1);
});

it('tidak memanggil AI pemilih bila tidak ada kandidat', function () {
    PengembangKataKunci::fake([['kata_kunci' => ['zakat']]]);
    PemilihHadist::fake()->preventStrayPrompts();

    $hasil = (new CariHadist)('zakat');

    expect($hasil->hadist)->toBeEmpty()->and($hasil->ringkasan)->toBeNull();
    PemilihHadist::assertNeverPrompted();
});
