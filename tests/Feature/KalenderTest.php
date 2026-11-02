<?php

use App\Support\LiburNasional;
use Illuminate\Support\Facades\Http;

// Potongan ICS Google: libur nasional, cuti bersama, dan "Perayaan" (bukan libur) yang harus dibuang.
function icsLibur(): string
{
    $acara = fn (string $tanggal, string $nama, string $jenis) => "BEGIN:VEVENT\r\nDTSTART;VALUE=DATE:{$tanggal}\r\nDESCRIPTION:{$jenis}\r\nSUMMARY:{$nama}\r\nEND:VEVENT\r\n";

    return "BEGIN:VCALENDAR\r\n"
        .$acara('20261225', 'Hari Raya Natal', 'Hari libur nasional')
        .$acara('20261224', 'Cuti Bersama Natal (Malam Natal)', 'Hari libur nasional')
        .$acara('20261231', 'Malam Tahun Baru', 'Perayaan\nUntuk menyembunyikan kalender perayaan\, buka Setelan')
        ."BEGIN:VEVENT\r\nDTSTART;VALUE=DATE:20260817\r\nDESCRIPTION:Hari libur nasional\r\nSUMMARY:Hari Proklamasi Kemerde\r\n kaan R.I.\r\nEND:VEVENT\r\n"
        .'END:VCALENDAR';
}

beforeEach(function () {
    Http::preventStrayRequests();
});

function pakaiLibur(): void
{
    Http::fake([LiburNasional::URL => Http::response(icsLibur())]);
}

it('mengurai libur nasional dari ICS dan membuang perayaan', function () {
    expect(LiburNasional::urai(icsLibur()))->toBe([
        '2026-08-17' => ['Hari Proklamasi Kemerdekaan R.I.'],
        '2026-12-24' => ['Cuti Bersama Natal (Malam Natal)'],
        '2026-12-25' => ['Hari Raya Natal'],
    ]);
});

it('menampilkan kalender Masehi dengan tanggal Hijriah', function () {
    pakaiLibur();
    $this->get('/kalender?bulan=2026-11')->assertOk()
        ->assertSee('November 2026')
        ->assertSee('Jumadilawal 1448 H – Jumadilakhir 1448 H')
        ->assertSee('title="22 Jumadilawal 1448 H"', false) // 2 November 2026
        ->assertSee('href="/kalender?bulan=2026-10"', false)
        ->assertSee('href="/kalender?bulan=2026-12"', false);
});

it('mendaftar libur nasional, cuti bersama, dan hari besar Islam bulan itu', function () {
    pakaiLibur();
    // Desember 2026 ≈ Jumadilakhir–Rajab 1448: tidak ada hari besar Islam; Agustus 2026 ada Maulid (12 Rabiul Awal 1448).
    $this->get('/kalender?bulan=2026-12')->assertOk()
        ->assertSeeInOrder(['Cuti Bersama Natal (Malam Natal)', 'Cuti bersama', 'Hari Raya Natal', 'Libur nasional'])
        ->assertSee('title="Hari Raya Natal"', false)
        ->assertDontSee('Malam Tahun Baru');

    $this->get('/kalender?bulan=2026-08')->assertOk()
        ->assertSee('Hari Proklamasi Kemerdekaan R.I.')
        ->assertSee('Maulid Nabi Muhammad ﷺ (12 Rabiulawal 1448 H)')
        ->assertSee('Hari Islam');
});

it('kalender tetap tampil saat data libur tidak bisa dimuat', function () {
    Http::fake([LiburNasional::URL => Http::response('', 500)]);

    $this->get('/kalender?bulan=2026-12')->assertOk()
        ->assertSee('Data libur nasional sedang tidak bisa dimuat')
        ->assertSee('Tidak ada libur nasional atau hari besar Islam di bulan ini.');
});

it('menyorot hari ini dan kembali ke bulan berjalan bila parameter tidak sah', function (string $bulan) {
    pakaiLibur();
    $this->travelTo(now('Asia/Jakarta')->setDate(2026, 3, 15));

    $this->get('/kalender?bulan='.$bulan)->assertOk()->assertSee('Maret 2026')->assertSee('aria-current="date"', false);
})->with(['', '2026-13', 'abc', '1800-01']);

it('menautkan kalender dan tentang kami di footer dan widget, bukan di header', function () {
    $halaman = $this->get('/')->assertOk()->getContent();
    $bagian = fn (string $dari, string $sampai) => str($halaman)->between($dari, $sampai)->toString();

    expect($bagian('<header', '</header>'))->not->toContain('/kalender')->not->toContain('/tentang')
        ->and($bagian('<footer', '</footer>'))->toContain('href="/kalender"')->toContain('href="/tentang"')
        ->and($bagian('data-widget-sholat', '</section>'))->toContain('href="/kalender"');
});

it('halaman tentang kami menyebut Sayogi Creative Agency', function () {
    $this->get('/tentang')->assertOk()->assertSee('Sayogi Creative Agency')->assertSee('<title>Tentang kami — Sayogi Mengaji</title>', false);
});
