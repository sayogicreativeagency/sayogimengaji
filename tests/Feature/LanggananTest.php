<?php

use App\Models\User;

it('akun tanpa langganan tidak berlangganan', function () {
    expect(User::factory()->create()->berlangganan())->toBeFalse();
});

it('langganan aktif sampai tanggal berakhirnya', function () {
    $wanda = User::factory()->create();
    $wanda->langganan()->create(['berakhir_pada' => now()->addDay(), 'sumber' => 'manual']);
    expect($wanda->berlangganan())->toBeTrue();

    $this->travel(2)->days();
    expect($wanda->fresh()->berlangganan())->toBeFalse();
});

it('command akun:langganan memberi atau memperpanjang langganan', function () {
    $wanda = User::factory()->create(['email' => 'wanda@example.com']);

    $this->artisan('akun:langganan', ['email' => 'wanda@example.com', '--bulan' => 12])->assertSuccessful();
    expect($wanda->fresh()->langganan->berakhir_pada->isSameDay(now()->addMonths(12)))->toBeTrue();

    $this->artisan('akun:langganan', ['email' => 'wanda@example.com', '--bulan' => 1])->assertSuccessful();
    expect($wanda->fresh()->langganan->berakhir_pada->isSameDay(now()->addMonths(13)))->toBeTrue();

    $this->artisan('akun:langganan', ['email' => 'tidakada@example.com'])->assertFailed();
});
