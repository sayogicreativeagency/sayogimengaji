<?php

use App\Models\Ayat;
use App\Models\Kata;
use App\Models\ProgresBelajar;
use App\Models\Surat;
use App\Models\User;
use App\Models\WaktuKata;

beforeEach(function () {
    foreach ([[1, 'Al-Fatihah', 2], [2, 'Al-Baqarah', 3]] as [$nomor, $nama, $jumlah]) {
        Surat::create(['nomor' => $nomor, 'nama' => 'سورة', 'nama_latin' => $nama, 'arti' => 'x', 'tempat_turun' => 'Mekah', 'jumlah_ayat' => $jumlah, 'deskripsi' => '-']);
        foreach (range(1, $jumlah) as $a) {
            Ayat::create(['surat_nomor' => $nomor, 'nomor' => $a, 'teks_arab' => 'ع', 'teks_latin' => "latin {$nomor}:{$a}", 'terjemahan' => "arti {$nomor}:{$a}"]);
            foreach ([1, 2] as $p) {
                Kata::create(['surat_nomor' => $nomor, 'ayat_nomor' => $a, 'posisi' => $p, 'teks' => $p === 1 ? 'بِسْمِ' : 'ٱللَّهِ']);
                WaktuKata::create(['qori' => 'afasi', 'surat_nomor' => $nomor, 'ayat_nomor' => $a, 'posisi' => $p, 'mulai_ms' => $p * 100, 'selesai_ms' => $p * 100 + 90]);
            }
        }
    }
    $this->pengguna = User::factory()->create();
});

it('meminta login untuk belajar', function () {
    $this->get('/belajar')->assertRedirect('/masuk');
    $this->get('/belajar/1')->assertRedirect('/masuk');
});

it('semua surat terbuka gratis untuk yang login, lengkap dengan kata dan waktunya', function () {
    $this->actingAs($this->pengguna)->get('/belajar/2')->assertOk()
        ->assertSee('data-belajar', false)
        ->assertSee('data-posisi="2"', false)
        ->assertSee('ٱللَّهِ')
        ->assertSee('latin 2:3')
        ->assertSee('"afasi":[[1,100,190],[2,200,290]]', false);
});

it('menyimpan progres hafalan milik sendiri', function () {
    $this->actingAs($this->pengguna)->postJson('/belajar/progres', ['surat' => 2, 'ayat' => 3, 'status' => 'hafal'])->assertNoContent();
    $this->actingAs($this->pengguna)->postJson('/belajar/progres', ['surat' => 2, 'ayat' => 2, 'status' => 'didengar'])->assertNoContent();

    expect(ProgresBelajar::where('user_id', $this->pengguna->id)->where('ayat_nomor', 3)->sole()->dihafal_at)->not->toBeNull();

    $this->actingAs($this->pengguna)->postJson('/belajar/progres', ['surat' => 2, 'ayat' => 3, 'status' => 'belum-hafal'])->assertNoContent();
    expect(ProgresBelajar::where('ayat_nomor', 3)->sole()->dihafal_at)->toBeNull();
});

it('menolak progres untuk ayat di luar jangkauan atau status tidak dikenal', function () {
    $this->actingAs($this->pengguna)->postJson('/belajar/progres', ['surat' => 2, 'ayat' => 9, 'status' => 'hafal'])->assertUnprocessable();
    $this->actingAs($this->pengguna)->postJson('/belajar/progres', ['surat' => 2, 'ayat' => 1, 'status' => 'raja'])->assertUnprocessable();
});

it('dasbor menampilkan persentase hafalan dan tombol lanjutkan', function () {
    $this->actingAs($this->pengguna)->postJson('/belajar/progres', ['surat' => 2, 'ayat' => 1, 'status' => 'hafal']);
    $this->actingAs($this->pengguna)->postJson('/belajar/progres', ['surat' => 2, 'ayat' => 2, 'status' => 'didengar']);

    $this->actingAs($this->pengguna)->get('/belajar')->assertOk()
        ->assertSee('Lanjutkan')
        ->assertSee('href="'.url('/belajar/2').'#ayat-2"', false)
        ->assertSee('1/3 ayat hafal');
});

it('menautkan Belajar di header', function () {
    $this->get('/')->assertSee('href="/belajar"', false);
});
