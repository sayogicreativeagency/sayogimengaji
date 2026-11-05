<?php

use App\Actions\ImporDoa;
use App\Models\Doa;
use Illuminate\Support\Facades\Http;

function buatDoa(array $ubah = []): Doa
{
    return Doa::create([
        'id' => 1, 'grup' => 'Doa Sebelum dan Sesudah Tidur', 'nama' => 'Doa Sebelum Tidur', 'arab' => 'بِاسْمِكَ رَبِّيْ',
        'latin' => 'Bismika robbii', 'arti' => 'Dengan nama Engkau, wahai Tuhanku.', 'tentang' => 'HR. Al-Bukhari 11/126.', ...$ubah,
    ]);
}

it('impor: melewati doa tanpa arti, merapikan grup, dan membuang tautan Facebook', function () {
    Http::fake(['equran.id/api/doa' => Http::response(['status' => 'success', 'data' => [
        ['id' => 1, 'grup' => 'Ucapan Hari Raya.', 'nama' => ' Takbir ', 'ar' => 'الله أكبر', 'tr' => 'Allahu akbar', 'idn' => 'Allah Maha Besar', 'tentang' => "HR. Muslim no. 1.\nSumber: https://www.facebook.com/photo.php?fbid=1&set=a.2"],
        ['id' => 2, 'grup' => 'Kosong', 'nama' => 'Tanpa arti', 'ar' => 'الله', 'tr' => '', 'idn' => '', 'tentang' => ''],
        ['id' => 3, 'grup' => 'Lain', 'nama' => 'Hanya Facebook', 'ar' => 'الله', 'tr' => 'Allah', 'idn' => 'Allah', 'tentang' => 'Sumber: https://www.facebook.com/photo.php?fbid=3'],
    ]])]);

    expect(app(ImporDoa::class)())->toBe(2)
        ->and(Doa::find(2))->toBeNull()
        ->and(Doa::find(1))->grup->toBe('Ucapan Hari Raya')->nama->toBe('Takbir')->tentang->toBe('HR. Muslim no. 1.')
        ->and(Doa::find(3)->tentang)->toBeNull();

    // Diimpor ulang: baris lama diperbarui, bukan bertambah.
    app(ImporDoa::class)();
    expect(Doa::count())->toBe(2);
});

it('daftar doa dikelompokkan per grup dan bisa disaring', function () {
    buatDoa();
    buatDoa(['id' => 40, 'grup' => 'Doa Perjalanan', 'nama' => 'Doa Naik Kendaraan']);

    $this->get('/doa')->assertOk()
        ->assertSeeInOrder(['Doa Sebelum dan Sesudah Tidur', 'Doa Sebelum Tidur', 'Doa Perjalanan', 'Doa Naik Kendaraan'])
        ->assertSee('id="doa-perjalanan"', false)
        ->assertSee('href="'.url('/doa/40').'"', false)
        ->assertSee('data-saring-doa', false)
        ->assertSee('<title>Doa Harian: Arab, Latin, dan Artinya — Sayogi Mengaji</title>', false)
        ->assertSee('href="https://equran.id"', false);
});

it('halaman doa menampilkan arab, latin, arti, rujukan, dan doa lain di grup yang sama, tanpa perlu akun', function () {
    buatDoa();
    buatDoa(['id' => 2, 'nama' => 'Doa Bangun Tidur']);
    buatDoa(['id' => 40, 'grup' => 'Doa Perjalanan', 'nama' => 'Doa Naik Kendaraan']);

    $this->get('/doa/1')->assertOk()
        ->assertSeeInOrder(['Doa Sebelum Tidur', 'بِاسْمِكَ رَبِّيْ', 'Bismika robbii', 'Dengan nama Engkau', 'HR. Al-Bukhari 11/126.', 'Doa Bangun Tidur'])
        ->assertSee('<meta name="description" content="Dengan nama Engkau, wahai Tuhanku.">', false)
        ->assertSee('href="'.url('/doa#doa-sebelum-dan-sesudah-tidur').'"', false)
        ->assertDontSee('Doa Naik Kendaraan');
});

it('keterangan dari sumber di-escape; hanya alamat rujukan yang jadi tautan', function () {
    buatDoa(['tentang' => '<script>alert(1)</script> Sumber: https://rumaysho.com/a?b=1&c=2']);

    $this->get('/doa/1')->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('&lt;script&gt;', false)
        ->assertSee('<a href="https://rumaysho.com/a?b=1&amp;c=2" rel="nofollow noopener" target="_blank">', false);
});

it('doa yang tidak ada: 404', function () {
    $this->get('/doa/999')->assertNotFound();
});

it('menu dan sitemap memuat doa', function () {
    buatDoa();

    $this->get('/kontak')->assertSee('href="/doa"', false);
    $this->get('/sitemap.xml')->assertOk()
        ->assertSee('<loc>'.url('/doa').'</loc>', false)
        ->assertSee('<loc>'.url('/doa/1').'</loc>', false);
});
