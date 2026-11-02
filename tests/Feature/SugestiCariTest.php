<?php

use App\Actions\BuatSugestiCari;
use App\Ai\Agents\PemilihHadist;
use App\Ai\Agents\PengembangKataKunci;
use App\Ai\Agents\PerapiSugesti;
use App\Enums\Peran;
use App\Filament\Pages\RisetPencarian;
use App\Models\Hadist;
use App\Models\PencarianHarian;
use App\Models\Pengaturan;
use App\Models\User;
use Livewire\Livewire;

/** @param  array<string, int>  $kata  kata → jumlah dicari (hari ini) */
function isiPencarian(array $kata, ?string $tanggal = null): void
{
    foreach ($kata as $k => $n) {
        PencarianHarian::create(['tanggal' => $tanggal ?? today()->toDateString(), 'kata' => $k, 'jumlah' => $n, 'jumlah_tanpa_hasil' => 0]);
    }
}

it('mencatat tiap pencarian per hari tanpa identitas, termasuk yang tanpa hasil', function () {
    $sabar = Hadist::factory()->create(['terjemahan' => 'Kesabaran adalah cahaya']);
    PengembangKataKunci::fake(fn (string $prompt) => ['kata_kunci' => str_contains($prompt, 'sabar') ? ['kesabaran'] : ['zzzz']]);
    PemilihHadist::fake(fn () => ['ringkasan' => 'ok', 'hadist_ids' => [$sabar->id]]);

    $this->get('/cari?q=Keutamaan++Sabar')->assertOk();
    $this->get('/cari?q=keutamaan sabar')->assertOk();
    $this->get('/cari?q=xyzabc')->assertOk();

    expect(PencarianHarian::where('kata', 'keutamaan sabar')->sole()->only('jumlah', 'jumlah_tanpa_hasil'))->toBe(['jumlah' => 2, 'jumlah_tanpa_hasil' => 0])
        ->and(PencarianHarian::where('kata', 'xyzabc')->sole()->only('jumlah', 'jumlah_tanpa_hasil'))->toBe(['jumlah' => 1, 'jumlah_tanpa_hasil' => 1])
        ->and(Schema::getColumnListing('pencarian_harian'))->not->toContain('user_id')->not->toContain('ip');
});

it('menyusun sugesti lewat LLM hanya dari kata yang cukup sering dan tidak disembunyikan', function () {
    isiPencarian(['keutamaan sabar' => 9, 'hadis sabar' => 4, 'adab makan' => 5, 'doa tidur' => 3, 'ruqyah' => 3, 'nama tetangga saya' => 2, 'kata terlarang' => 7]);
    Pengaturan::simpan('sugesti_disembunyikan', ['kata terlarang']);
    PerapiSugesti::fake([['sugesti' => ['Keutamaan Sabar.', 'adab makan', 'keutamaan sabar', 'kata terlarang', '  doa   sebelum tidur ', 'ruqyah']]]);

    $sugesti = (new BuatSugestiCari)();

    expect($sugesti)->toBe(['keutamaan sabar', 'adab makan', 'doa sebelum tidur', 'ruqyah'])
        ->and(Pengaturan::ambil('sugesti_cari'))->toBe($sugesti);
    PerapiSugesti::assertPrompted(fn ($p) => str_contains($p->prompt, 'keutamaan sabar (9)')
        && ! str_contains($p->prompt, 'nama tetangga saya')
        && ! str_contains($p->prompt, 'kata terlarang'));

    $this->get('/')->assertSeeInOrder(['href="/cari?q=keutamaan+sabar"', 'href="/cari?q=adab+makan"'], false)->assertDontSee('larangan marah');
    $this->get('/hadist')->assertSee('href="/cari?q=ruqyah"', false);
});

it('memakai kata teratas apa adanya bila LLM gagal', function () {
    isiPencarian(['sabar' => 9, 'adab makan' => 5, 'doa tidur' => 4, 'ruqyah' => 3]);
    PerapiSugesti::fake(fn () => throw new RuntimeException('timeout'));

    expect((new BuatSugestiCari)())->toBe(['sabar', 'adab makan', 'doa tidur', 'ruqyah']);
});

it('kembali ke daftar bawaan bila data pencarian belum cukup, dan tidak memanggil LLM', function () {
    isiPencarian(['sabar' => 9, 'adab makan' => 1]);
    Pengaturan::simpan('sugesti_cari', ['sugesti lama']);
    PerapiSugesti::fake()->preventStrayPrompts();

    expect((new BuatSugestiCari)())->toBe([]);
    PerapiSugesti::assertNeverPrompted();
    $this->get('/')->assertSee('href="/cari?q=larangan+marah"', false)->assertDontSee('sugesti lama');
});

it('command harian menghapus catatan lebih dari 90 hari dan dijadwalkan', function () {
    isiPencarian(['lama' => 5], today()->subDays(91)->toDateString());
    isiPencarian(['baru' => 5], today()->subDays(89)->toDateString());

    $this->artisan('pencarian:sugesti')->assertSuccessful();

    expect(PencarianHarian::pluck('kata')->all())->toBe(['baru']);
    $this->artisan('schedule:list')->expectsOutputToContain('pencarian:sugesti');
});

it('superadmin melihat riset pencarian dan menyembunyikan kata dari sugesti', function () {
    isiPencarian(['keutamaan sabar' => 9, 'kata aneh' => 4]);
    PencarianHarian::where('kata', 'kata aneh')->update(['jumlah_tanpa_hasil' => 4]);
    Pengaturan::simpan('sugesti_cari', ['keutamaan sabar', 'kata aneh']);
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]));

    Livewire::test(RisetPencarian::class)
        ->assertCanSeeTableRecords(['keutamaan sabar', 'kata aneh'])
        ->filterTable('tanpa_hasil')
        ->assertCanSeeTableRecords(['kata aneh'])
        ->assertCanNotSeeTableRecords(['keutamaan sabar'])
        ->removeTableFilters()
        ->callTableAction('sembunyikan', 'kata aneh');

    expect(Pengaturan::ambil('sugesti_disembunyikan'))->toBe(['kata aneh'])
        ->and(Pengaturan::ambil('sugesti_cari'))->toBe(['keutamaan sabar']);
});

it('editor tidak bisa membuka riset pencarian', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Editor]))->get('/admin/riset-pencarian')->assertForbidden();
});
