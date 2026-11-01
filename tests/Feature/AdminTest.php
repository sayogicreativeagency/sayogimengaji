<?php

use App\Enums\Derajat;
use App\Filament\Resources\Hadists\Pages\EditHadist;
use App\Filament\Resources\Hadists\Pages\ListHadists;
use App\Models\Hadist;
use App\Models\User;
use Livewire\Livewire;

it('meminta login untuk masuk admin', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('menampilkan daftar hadist', function () {
    $hadist = Hadist::factory()->create();
    $this->actingAs(User::factory()->create());

    Livewire::test(ListHadists::class)->assertOk()->assertCanSeeTableRecords([$hadist]);
});

it('menandai hadist yang disimpan admin sebagai dikoreksi', function () {
    $hadist = Hadist::factory()->create(['derajat_ringkas' => null]);
    $this->actingAs(User::factory()->create());

    Livewire::test(EditHadist::class, ['record' => $hadist->getRouteKey()])
        ->fillForm(['terjemahan' => 'Terjemahan koreksi', 'derajat_ringkas' => 'hasan'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($hadist->refresh())
        ->terjemahan->toBe('Terjemahan koreksi')
        ->derajat_ringkas->toBe(Derajat::Hasan)
        ->dikoreksi_at->not->toBeNull();
});
