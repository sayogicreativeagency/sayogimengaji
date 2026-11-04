<?php

use App\Enums\Peran;
use App\Filament\Pages\StatistikMurottal;
use App\Models\PutarMurottal;
use App\Models\Surat;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);
    Surat::create(['nomor' => 112, 'nama' => 'الإخلاص', 'nama_latin' => 'Al-Ikhlas', 'arti' => 'Ikhlas', 'tempat_turun' => 'Mekah', 'jumlah_ayat' => 4, 'deskripsi' => '-']);
    Surat::create(['nomor' => 36, 'nama' => 'يٰسۤ', 'nama_latin' => 'Yasin', 'arti' => 'Yasin', 'tempat_turun' => 'Mekah', 'jumlah_ayat' => 83, 'deskripsi' => '-']);
});

function laporanPutar(array $ubah = []): array
{
    return [...['sesi' => (string) Str::uuid(), 'surat' => 112, 'ayat_mulai' => 0, 'ayat_terakhir' => 0, 'detik' => 0, 'qori' => 'afasi', 'sumber' => 'surat', 'selesai' => 0], ...$ubah];
}

/** @param  array<string, mixed>  $ubah */
function buatPutar(array $ubah = []): PutarMurottal
{
    return PutarMurottal::create([
        'sesi' => (string) Str::uuid(), 'ip' => '10.0.0.1', 'surat_nomor' => 112, 'ayat_mulai' => 0, 'ayat_terakhir' => 2,
        'detik' => 60, 'qori' => 'afasi', 'sumber' => 'surat', 'perangkat' => 'HP', ...$ubah,
    ]);
}

it('sesi yang sama memperbarui satu baris; IP, ayat mulai, dan perangkat tetap dari laporan pertama', function () {
    $laporan = laporanPutar();
    $this->withHeader('User-Agent', 'Mozilla/5.0 (Linux; Android 14) Mobile')->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
        ->post('/quran/putar', $laporan)->assertNoContent();

    $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0)')->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])
        ->post('/quran/putar', [...$laporan, 'ayat_mulai' => 3, 'ayat_terakhir' => 4, 'detik' => 30, 'selesai' => 1])->assertNoContent();

    expect(PutarMurottal::count())->toBe(1);
    expect(PutarMurottal::first())
        ->ip->toBe('10.0.0.1')->perangkat->toBe('HP')->ayat_mulai->toBe(0)
        ->ayat_terakhir->toBe(4)->detik->toBe(30)->selesai->toBeTrue();
});

it('mencatat akun bila sedang masuk', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/quran/putar', laporanPutar())->assertNoContent();

    expect(PutarMurottal::first()->user_id)->toBe($user->id);
});

it('menolak laporan yang tidak masuk akal', function (array $ubah) {
    $this->post('/quran/putar', laporanPutar($ubah))->assertRedirect();

    expect(PutarMurottal::count())->toBe(0);
})->with([
    'ayat melebihi jumlah ayat surat' => [['ayat_terakhir' => 5]],
    'qori tidak dikenal' => [['qori' => 'siapa']],
    'sesi bukan uuid' => [['sesi' => 'abc']],
    'sumber tidak dikenal' => [['sumber' => 'lain']],
]);

it('surat yang tidak ada ditolak', function () {
    $this->post('/quran/putar', laporanPutar(['surat' => 999]))->assertNotFound();
});

it('hari ini dihitung dari tengah malam WIB, 24 jam dan 7 hari bergulir', function () {
    // 01.30 WIB tanggal 5 November; hari ini baru dimulai satu setengah jam lalu.
    $this->travelTo(Carbon::parse('2026-11-04 18:30:00', 'UTC'));

    buatPutar(['created_at' => '2026-11-04 17:30:00', 'selesai' => true, 'detik' => 100, 'ip' => '10.0.0.1']);
    buatPutar(['created_at' => '2026-11-04 16:30:00', 'detik' => 50, 'ip' => '10.0.0.1']);
    buatPutar(['created_at' => '2026-11-01 10:00:00', 'detik' => 25, 'ip' => '10.0.0.2']);
    buatPutar(['created_at' => '2026-10-20 10:00:00', 'detik' => 10, 'ip' => '10.0.0.3']);

    expect((array) PutarMurottal::ringkasan('hari_ini'))->toBe(['diputar' => 1, 'selesai' => 1, 'pendengar' => 1, 'detik' => 100])
        ->and((array) PutarMurottal::ringkasan('24jam'))->toBe(['diputar' => 2, 'selesai' => 1, 'pendengar' => 1, 'detik' => 150])
        ->and((array) PutarMurottal::ringkasan('7hari'))->toBe(['diputar' => 3, 'selesai' => 1, 'pendengar' => 2, 'detik' => 175]);
});

it('per surat: urut terbanyak dan menunjukkan ayat tempat paling sering berhenti', function () {
    buatPutar(['surat_nomor' => 36, 'ayat_terakhir' => 12]);
    buatPutar(['surat_nomor' => 36, 'ayat_terakhir' => 12, 'ip' => '10.0.0.2']);
    buatPutar(['surat_nomor' => 36, 'ayat_terakhir' => 40]);
    buatPutar(['surat_nomor' => 112, 'ayat_terakhir' => 4, 'selesai' => true]);

    [$yasin, $ikhlas] = PutarMurottal::perSurat('24jam')->all();

    expect($yasin->surat_nomor)->toBe(36)
        ->and((int) $yasin->diputar)->toBe(3)
        ->and((int) $yasin->pendengar)->toBe(2)
        ->and((int) $yasin->berhenti_terbanyak->ayat_terakhir)->toBe(12)
        ->and((int) $yasin->berhenti_terbanyak->jumlah)->toBe(2)
        ->and($ikhlas->surat_nomor)->toBe(112)
        ->and($ikhlas->berhenti_terbanyak)->toBeNull();
});

it('superadmin melihat statistik dan ganti periode menyaring rincian', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]));
    $baru = buatPutar(['surat_nomor' => 36, 'ayat_mulai' => 0, 'ayat_terakhir' => 12, 'ip' => '10.1.1.1']);
    $lama = buatPutar(['created_at' => now()->subDays(3), 'ip' => '10.2.2.2', 'selesai' => true]);

    Livewire::test(StatistikMurottal::class)
        ->assertSee('24 jam terakhir')->assertSee('Yasin')->assertSee('Ayat 12')->assertSee('Misyari Rasyid Al-Afasi')
        ->assertCanSeeTableRecords([$baru])->assertCanNotSeeTableRecords([$lama])
        ->set('periode', '7hari')
        ->assertCanSeeTableRecords([$baru, $lama])
        ->assertSee('Al-Ikhlas');
});

it('statistik memuat IP: editor tidak bisa membukanya', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Editor]))->get('/admin/statistik-murottal')->assertForbidden();
});

it('kebijakan privasi menyebut statistik murottal dan masa simpannya', function () {
    $this->get('/kebijakan-privasi')->assertOk()
        ->assertSee('Statistik murottal')
        ->assertSee('statistik murottal terhapus setelah 90 hari');
});
