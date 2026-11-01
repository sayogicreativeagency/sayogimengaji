<?php

use App\Support\Qris;

// Payload contoh (bukan QRIS sungguhan), CRC dihitung oleh Qris::denganCrc.
function qrisContoh(string $nama = 'HADIST APA'): string
{
    $nama = sprintf('59%02d%s', strlen($nama), $nama);

    return Qris::denganCrc('000201010211520448295303360'.'5802ID'.$nama.'6007JAKARTA6304');
}

it('menerima payload dengan CRC benar dan membaca nama penerima', function () {
    $qris = new Qris(qrisContoh());

    expect($qris->valid())->toBeTrue()->and($qris->namaPenerima())->toBe('HADIST APA');
});

it('menolak payload yang CRC-nya tidak cocok', function () {
    $rusak = substr(qrisContoh(), 0, -4).'0000';

    expect((new Qris($rusak))->valid())->toBeFalse();
});

it('menolak teks yang bukan QRIS', function (string $teks) {
    expect((new Qris($teks))->valid())->toBeFalse();
})->with(['', 'https://saweria.co/hadistapa', '000201']);
