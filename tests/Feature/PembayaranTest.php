<?php

use App\Enums\Peran;
use App\Enums\StatusBayar;
use App\Filament\Pages\PengaturanLangganan;
use App\Filament\Resources\Pembayarans\Pages\ManagePembayarans;
use App\Models\Langganan;
use App\Models\Pembayaran;
use App\Models\Pengaturan;
use App\Models\Surat;
use App\Models\User;
use App\Support\Qris;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    Surat::create(['nomor' => 2, 'nama' => 'سورة', 'nama_latin' => 'Al-Baqarah', 'arti' => 'x', 'tempat_turun' => 'Madinah', 'jumlah_ayat' => 1, 'deskripsi' => '-']);
    $this->pengguna = User::factory()->create();
});

it('saklar langganan mati membuka semua surat gratis dan menutup halaman pembayaran', function () {
    Pengaturan::simpan('langganan_berbayar', false);

    $this->actingAs($this->pengguna)->get('/belajar/2')->assertOk()->assertDontSee('Fitur langganan');
    $this->actingAs($this->pengguna)->get('/belajar')->assertSee('Semua surat terbuka gratis')->assertDontSee('🔒');
    $this->actingAs($this->pengguna)->get('/langganan')->assertNotFound();
    $this->actingAs($this->pengguna)->post('/langganan', ['bulan' => 1])->assertNotFound();
});

it('halaman terkunci menawarkan langganan dengan harganya', function () {
    $this->actingAs($this->pengguna)->get('/belajar/2')->assertSee('Rp 33.000 per bulan')->assertSee('href="/langganan"', false);
});

it('memesan membuat tagihan bernominal unik dan memakai ulang tagihan yang belum dikonfirmasi', function () {
    Pengaturan::simpan('langganan_harga', 30000);

    $this->actingAs($this->pengguna)->post('/langganan', ['bulan' => 3])->assertRedirect('/langganan/bayar/1');
    $tagihan = Pembayaran::sole();
    expect($tagihan->nominal)->toBeGreaterThan(90000)->toBeLessThan(91000)
        ->and($tagihan->status)->toBe(StatusBayar::Menunggu);

    $this->actingAs($this->pengguna)->post('/langganan', ['bulan' => 3])->assertRedirect('/langganan/bayar/1');
    expect(Pembayaran::sole()->nominal)->toBe($tagihan->nominal);

    $this->actingAs($this->pengguna)->post('/langganan', ['bulan' => 12])->assertRedirect('/langganan/bayar/1');
    expect(Pembayaran::sole()->bulan)->toBe(12);

    $this->actingAs($this->pengguna)->post('/langganan', ['bulan' => 2])->assertSessionHasErrors('bulan');
});

it('halaman bayar menampilkan nominal, QRIS bernominal, dan rekening', function () {
    config([
        'services.infaq.qris' => Qris::denganCrc('000201010211520448295303360'.'5802ID'.'5910HADIST APA'.'6007JAKARTA6304'),
        'services.pembayaran.rekening' => 'BCA 123 a.n. Hadist Apa',
    ]);
    $tagihan = $this->pengguna->pembayaran()->create(['bulan' => 1, 'nominal' => 33127]);

    $this->actingAs($this->pengguna)->get('/langganan/bayar/'.$tagihan->id)->assertOk()
        ->assertSee('Rp 33.127')->assertSee('QRIS pembayaran Rp 33.127', false)->assertSee('BCA 123 a.n. Hadist Apa');
});

it('tagihan orang lain tidak bisa dilihat atau dikonfirmasi', function () {
    $tagihan = User::factory()->create()->pembayaran()->create(['bulan' => 1, 'nominal' => 33127]);

    $this->actingAs($this->pengguna)->get('/langganan/bayar/'.$tagihan->id)->assertNotFound();
    $this->actingAs($this->pengguna)->post('/langganan/bayar/'.$tagihan->id, ['metode' => 'qris'])->assertNotFound();
});

it('konfirmasi menyimpan metode, pengirim, dan bukti di disk privat', function () {
    Storage::fake('local');
    $tagihan = $this->pengguna->pembayaran()->create(['bulan' => 1, 'nominal' => 33127]);

    $this->actingAs($this->pengguna)->post('/langganan/bayar/'.$tagihan->id, [
        'metode' => 'transfer', 'pengirim' => 'Wanda', 'bukti' => UploadedFile::fake()->image('bukti.jpg'),
    ])->assertRedirect()->assertSessionHas('status');

    $tagihan->refresh();
    expect($tagihan->status)->toBe(StatusBayar::Dilaporkan)
        ->and($tagihan->metode)->toBe('transfer')
        ->and($tagihan->dilaporkan_at)->not->toBeNull();
    Storage::disk('local')->assertExists($tagihan->bukti);

    $this->actingAs($this->pengguna)->post('/langganan/bayar/'.$tagihan->id, ['metode' => 'kartu'])->assertSessionHasErrors('metode');
});

it('superadmin menyetujui pembayaran sehingga langganan bertambah, lalu tidak bisa disetujui dua kali', function () {
    $tagihan = $this->pengguna->pembayaran()->create(['bulan' => 3, 'nominal' => 99127, 'status' => StatusBayar::Dilaporkan, 'metode' => 'qris']);
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]));

    Livewire::test(ManagePembayarans::class)->callTableAction('setujui', $tagihan)->assertHasNoTableActionErrors();

    $langganan = $this->pengguna->fresh()->langganan;
    expect($tagihan->fresh()->status)->toBe(StatusBayar::Lunas)
        ->and($langganan->berakhir_pada->isSameDay(now()->addMonths(3)))->toBeTrue()
        ->and($langganan->sumber)->toBe('qris');
    expect(fn () => $tagihan->fresh()->setujui())->toThrow(LogicException::class);

    $this->actingAs($this->pengguna)->get('/belajar/2')->assertDontSee('Fitur langganan');
});

it('superadmin menolak pembayaran tanpa mengubah langganan', function () {
    $tagihan = $this->pengguna->pembayaran()->create(['bulan' => 1, 'nominal' => 33127]);
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]));

    Livewire::test(ManagePembayarans::class)->callTableAction('tolak', $tagihan);

    expect($tagihan->fresh()->status)->toBe(StatusBayar::Ditolak)->and($this->pengguna->fresh()->langganan)->toBeNull();
});

it('superadmin mengatur saklar dan harga langganan', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]));

    Livewire::test(PengaturanLangganan::class)
        ->assertFormSet(['berbayar' => true, 'harga' => 33000])
        ->fillForm(['berbayar' => false, 'harga' => 25000])
        ->call('simpan')
        ->assertHasNoFormErrors();

    expect(Langganan::berbayar())->toBeFalse()->and(Langganan::harga())->toBe(25000);
});

it('editor tidak bisa membuka pembayaran atau pengaturan langganan', function () {
    $this->actingAs(User::factory()->create(['peran' => Peran::Editor]));

    $this->get('/admin/pembayarans')->assertForbidden();
    $this->get('/admin/pengaturan-langganan')->assertForbidden();
});
