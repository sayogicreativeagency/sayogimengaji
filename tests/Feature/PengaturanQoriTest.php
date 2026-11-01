<?php

use App\Enums\Peran;
use App\Enums\QoriBelajar;
use App\Filament\Pages\PengaturanQori;
use App\Models\Ayat;
use App\Models\Kata;
use App\Models\Pengaturan;
use App\Models\Surat;
use App\Models\User;
use App\Models\WaktuKata;
use Livewire\Livewire;

beforeEach(function () {
    Surat::create(['nomor' => 1, 'nama' => 'الفاتحة', 'nama_latin' => 'Al-Fatihah', 'arti' => 'x', 'tempat_turun' => 'Mekah', 'jumlah_ayat' => 1, 'deskripsi' => '-']);
    Ayat::create(['surat_nomor' => 1, 'nomor' => 1, 'teks_arab' => 'ع', 'teks_latin' => 'l', 'terjemahan' => 't']);
    Kata::create(['surat_nomor' => 1, 'ayat_nomor' => 1, 'posisi' => 1, 'teks' => 'بِسْمِ']);
    foreach (QoriBelajar::cases() as $q) {
        WaktuKata::create(['qori' => $q->value, 'surat_nomor' => 1, 'ayat_nomor' => 1, 'posisi' => 1, 'mulai_ms' => 0, 'selesai_ms' => 500]);
    }
});

it('semua qori aktif secara bawaan', function () {
    expect(QoriBelajar::aktif())->toBe(QoriBelajar::cases());
});

it('qori yang dinonaktifkan tidak tampil di halaman belajar', function () {
    Pengaturan::simpan('qori_belajar_nonaktif', ['husary-muallim']);

    $this->actingAs(User::factory()->create())->get('/belajar/1')->assertOk()
        ->assertSee('<option value="afasi">', false)
        ->assertDontSee('<option value="husary-muallim">', false)
        ->assertDontSee('"husary-muallim":', false)
        ->assertSee('Quran data provided by');
});

it('superadmin mengatur qori nonaktif dari admin', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]));

    Livewire::test(PengaturanQori::class)
        ->assertFormSet(['nonaktif' => []])
        ->fillForm(['nonaktif' => ['husary-muallim']])
        ->call('simpan')
        ->assertHasNoFormErrors();

    expect(QoriBelajar::aktif())->toBe([QoriBelajar::Afasi]);
});

it('tidak boleh menonaktifkan semua qori', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]));

    Livewire::test(PengaturanQori::class)
        ->fillForm(['nonaktif' => array_column(QoriBelajar::cases(), 'value')])
        ->call('simpan')
        ->assertHasFormErrors(['nonaktif']);

    expect(QoriBelajar::aktif())->toBe(QoriBelajar::cases());
});

it('editor tidak bisa membuka pengaturan qori', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Editor]))->get('/admin/pengaturan-qori')->assertForbidden();
});
