<?php

use App\Enums\Peran;
use App\Filament\Resources\Laporans\Pages\ManageLaporans;
use App\Models\Laporan;
use App\Models\User;
use Livewire\Livewire;

it('menampilkan form kontak dengan query pencarian terisi', function () {
    $this->get('/kontak?q=hadist+tentang+pernikahan')->assertOk()
        ->assertSee('name="pesan"', false)
        ->assertSee('value="hadist tentang pernikahan"', false);
});

it('menyimpan laporan', function () {
    $this->post('/kontak', ['nama' => 'Fulan', 'email' => 'fulan@example.com', 'q' => 'pernikahan', 'pesan' => 'Ringkasan [3] tidak sesuai hadistnya.'])
        ->assertRedirect('/kontak')
        ->assertSessionHas('status');

    expect(Laporan::sole())->nama->toBe('Fulan')->q->toBe('pernikahan')->pesan->toBe('Ringkasan [3] tidak sesuai hadistnya.');
});

it('memvalidasi laporan', function () {
    $this->post('/kontak', ['email' => 'bukan-email', 'pesan' => ''])->assertSessionHasErrors(['email', 'pesan']);

    expect(Laporan::count())->toBe(0);
});

it('mengabaikan kiriman bot lewat honeypot', function () {
    $this->post('/kontak', ['pesan' => 'spam spam spam', 'situs' => 'http://spam.test'])->assertRedirect('/kontak');

    expect(Laporan::count())->toBe(0);
});

it('menampilkan laporan di admin', function () {
    $laporan = Laporan::create(['pesan' => 'Ada yang keliru']);
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]));

    Livewire::test(ManageLaporans::class)->assertOk()->assertCanSeeTableRecords([$laporan]);
});
