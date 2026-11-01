<?php

use App\Enums\Peran;
use App\Models\User;

it('menampilkan halaman masuk dan daftar dengan tampilan utama', function (string $url) {
    $this->get($url)->assertOk()
        ->assertSee('href="/hadist"', false)
        ->assertSee('name="email"', false)
        ->assertSee('name="password"', false);
})->with(['/masuk', '/daftar']);

it('pengguna masuk lalu kembali ke beranda', function () {
    $wanda = User::factory()->create(['email' => 'wanda@example.com', 'password' => 'rahasia-123']);

    $this->post('/masuk', ['email' => 'wanda@example.com', 'password' => 'rahasia-123'])->assertRedirect('/');
    $this->assertAuthenticatedAs($wanda);
});

it('superadmin dan editor diarahkan ke admin setelah masuk', function (Peran $peran) {
    User::factory()->create(['email' => 'admin@example.com', 'password' => 'rahasia-123', 'peran' => $peran]);

    $this->post('/masuk', ['email' => 'admin@example.com', 'password' => 'rahasia-123'])->assertRedirect('/admin');
})->with([Peran::Superadmin, Peran::Editor]);

it('kembali ke halaman yang diminta sebelum masuk', function () {
    User::factory()->create(['email' => 'admin@example.com', 'password' => 'rahasia-123', 'peran' => Peran::Superadmin]);

    $this->get('/admin/hadists')->assertRedirect('/masuk');
    $this->post('/masuk', ['email' => 'admin@example.com', 'password' => 'rahasia-123'])->assertRedirect('/admin/hadists');
});

it('menolak password salah', function () {
    User::factory()->create(['email' => 'wanda@example.com', 'password' => 'rahasia-123']);

    $this->from('/masuk')->post('/masuk', ['email' => 'wanda@example.com', 'password' => 'salah'])
        ->assertRedirect('/masuk')->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('membatasi percobaan masuk yang gagal', function () {
    User::factory()->create(['email' => 'wanda@example.com', 'password' => 'rahasia-123']);

    foreach (range(1, 5) as $_) {
        $this->post('/masuk', ['email' => 'wanda@example.com', 'password' => 'salah']);
    }

    $this->post('/masuk', ['email' => 'wanda@example.com', 'password' => 'rahasia-123'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('mendaftarkan akun baru sebagai pengguna walau mencoba menyisipkan peran', function () {
    $this->post('/daftar', [
        'name' => 'Wanda', 'email' => 'wanda@example.com', 'password' => 'rahasia-123',
        'password_confirmation' => 'rahasia-123', 'peran' => 'superadmin',
    ])->assertRedirect('/');

    $wanda = User::where('email', 'wanda@example.com')->sole();
    expect($wanda->peran)->toBe(Peran::Pengguna);
    $this->assertAuthenticatedAs($wanda);
});

it('memvalidasi pendaftaran', function () {
    User::factory()->create(['email' => 'wanda@example.com']);

    $this->post('/daftar', ['name' => '', 'email' => 'wanda@example.com', 'password' => 'pendek', 'password_confirmation' => 'beda'])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

it('keluar dari akun', function () {
    $this->actingAs(User::factory()->create())->post('/keluar')->assertRedirect('/');
    $this->assertGuest();
});

it('menampilkan menu akun di header sesuai peran', function () {
    $this->get('/')->assertSee('href="/masuk"', false);

    $this->actingAs(User::factory()->create(['name' => 'Wanda']))->get('/')
        ->assertSee('Wanda')->assertSee('action="/keluar"', false)->assertDontSee('href="/admin"', false);

    $this->actingAs(User::factory()->create(['peran' => Peran::Superadmin]))->get('/')->assertSee('href="/admin"', false);
});

it('pengguna yang tadinya membuka admin diarahkan ke beranda, bukan halaman 403', function () {
    User::factory()->create(['email' => 'wanda@example.com', 'password' => 'rahasia-123']);

    $this->get('/admin')->assertRedirect('/masuk');
    $this->post('/masuk', ['email' => 'wanda@example.com', 'password' => 'rahasia-123'])->assertRedirect('/');
});
