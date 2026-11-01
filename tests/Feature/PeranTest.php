<?php

use App\Enums\Peran;
use App\Filament\Resources\Hadists\Pages\EditHadist;
use App\Filament\Resources\Laporans\Pages\ManageLaporans;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Hadist;
use App\Models\Laporan;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

it('akun baru berperan pengguna', function () {
    expect(User::factory()->create()->refresh()->peran)->toBe(Peran::Pengguna);
});

it('hanya superadmin dan editor yang bisa masuk admin', function (Peran $peran, int $status) {
    $this->actingAs(User::factory()->create(['peran' => $peran]))->get('/admin')->assertStatus($status);
})->with([
    'superadmin' => [Peran::Superadmin, 200],
    'editor' => [Peran::Editor, 200],
    'pengguna' => [Peran::Pengguna, 403],
]);

it('tamu yang membuka admin diarahkan ke halaman masuk', function () {
    $this->get('/admin')->assertRedirect('/masuk');
});

it('editor bisa mengoreksi hadist dan membaca laporan tapi tidak menghapusnya', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Editor]));
    $hadist = Hadist::factory()->create();
    $laporan = Laporan::create(['pesan' => 'keliru']);

    Livewire::test(EditHadist::class, ['record' => $hadist->getRouteKey()])->assertOk();
    Livewire::test(ManageLaporans::class)->assertCanSeeTableRecords([$laporan])->assertActionHidden(TestAction::make('delete')->table($laporan));
});

it('editor tidak bisa membuka menu akun', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Editor]));

    $this->get('/admin/users')->assertForbidden();
});

it('superadmin bisa menghapus laporan dan mengelola akun', function () {
    $this->actingAs($admin = User::factory()->create(['peran' => Peran::Superadmin]));
    $laporan = Laporan::create(['pesan' => 'keliru']);
    $wanda = User::factory()->create(['name' => 'Wanda']);

    Livewire::test(ManageLaporans::class)->assertActionVisible(TestAction::make('delete')->table($laporan));
    Livewire::test(ListUsers::class)->assertCanSeeTableRecords([$admin, $wanda]);
    $this->get("/admin/users/{$wanda->id}/edit")->assertOk();
});

it('superadmin bisa mengganti peran akun lain tapi tidak perannya sendiri', function () {
    $this->actingAs($admin = User::factory()->create(['peran' => Peran::Superadmin]));
    $wanda = User::factory()->create();

    Livewire::test(EditUser::class, ['record' => $wanda->getRouteKey()])
        ->fillForm(['peran' => 'editor'])->call('save')->assertHasNoFormErrors();
    expect($wanda->refresh()->peran)->toBe(Peran::Editor);

    Livewire::test(EditUser::class, ['record' => $admin->getRouteKey()])
        ->assertFormFieldDisabled('peran');
});

it('command akun:peran mengatur peran lewat email', function () {
    $angga = User::factory()->create(['email' => 'angga@example.com']);

    $this->artisan('akun:peran', ['email' => 'angga@example.com', 'peran' => 'superadmin'])->assertSuccessful();
    expect($angga->refresh()->peran)->toBe(Peran::Superadmin);

    $this->artisan('akun:peran', ['email' => 'tidakada@example.com', 'peran' => 'editor'])->assertFailed();
    $this->artisan('akun:peran', ['email' => 'angga@example.com', 'peran' => 'raja'])->assertFailed();
});

it('tombol keluar di admin mengeluarkan akun', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]))->post('/admin/logout')->assertRedirect();
    $this->assertGuest();
});
