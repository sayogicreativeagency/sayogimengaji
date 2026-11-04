<?php

use App\Actions\BuatSugestiCari;
use App\Actions\ImporHadist;
use App\Actions\ImporKata;
use App\Actions\ImporQuran;
use App\Enums\Kitab;
use App\Enums\Peran;
use App\Enums\QoriBelajar;
use App\Models\PencarianHarian;
use App\Models\PutarMurottal;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('hadist:impor {kitab? : kode kitab, mis. bukhari; kosong = semua}', function (ImporHadist $impor) {
    $pilihan = $this->argument('kitab');
    $daftar = $pilihan ? [Kitab::tryFrom($pilihan)] : Kitab::cases();

    if ($daftar === [null]) {
        $this->error("Kitab tidak dikenal: {$pilihan}. Pilihan: ".implode(', ', array_column(Kitab::cases(), 'value')));

        return 1;
    }

    $gagal = false;
    foreach ($daftar as $kitab) {
        try {
            $this->info("{$kitab->label()}: ".$impor($kitab).' hadist');
        } catch (Throwable $e) {
            $gagal = true;
            $this->error("{$kitab->label()} gagal: {$e->getMessage()}");
        }
    }

    return $gagal ? 1 : 0;
})->purpose('Impor hadist dari dataset fawazahmed0/hadith-api');

Artisan::command('quran:impor {surat? : nomor surat 1-114; kosong = semua}', function (ImporQuran $impor) {
    $pilihan = $this->argument('surat');
    $daftar = $pilihan === null ? range(1, 114) : [(int) $pilihan];

    if ($daftar[0] < 1 || $daftar[0] > 114) {
        $this->error("Nomor surat harus 1-114, bukan {$pilihan}.");

        return 1;
    }

    $gagal = false;
    foreach ($daftar as $nomor) {
        try {
            $this->line("Surat {$nomor}: ".$impor($nomor).' ayat');
        } catch (Throwable $e) {
            $gagal = true;
            $this->error("Surat {$nomor} gagal: {$e->getMessage()}");
        }
    }

    return $gagal ? 1 : 0;
})->purpose('Impor teks Al-Qur\'an dari equran.id');

Artisan::command('akun:peran {email} {peran : superadmin, editor, atau pengguna}', function () {
    $peran = Peran::tryFrom($this->argument('peran'));
    $user = User::where('email', $this->argument('email'))->first();

    if (! $peran || ! $user) {
        $this->error(! $peran ? 'Peran harus superadmin, editor, atau pengguna.' : 'Akun dengan email itu tidak ditemukan.');

        return 1;
    }

    $user->forceFill(['peran' => $peran])->save();
    $this->info("{$user->name} sekarang {$peran->label()}.");

    return 0;
})->purpose('Atur peran akun (mis. jadikan superadmin pertama)');

Artisan::command('akun:langganan {email} {--bulan=12 : lama langganan, ditambahkan dari tanggal berakhir bila masih aktif}', function () {
    $user = User::where('email', $this->argument('email'))->first();

    if (! $user) {
        $this->error('Akun dengan email itu tidak ditemukan.');

        return 1;
    }

    $user->perpanjangLangganan((int) $this->option('bulan'), 'manual');
    $this->info("{$user->name} berlangganan sampai {$user->fresh()->langganan->berakhir_pada->translatedFormat('j F Y')}.");

    return 0;
})->purpose('Beri atau perpanjang langganan secara manual (sebelum pembayaran tersedia)');

Artisan::command('quran:impor-kata {surat? : nomor surat 1-114; kosong = semua}', function (ImporKata $impor) {
    $daftar = $this->argument('surat') === null ? range(1, 114) : [(int) $this->argument('surat')];
    $gagal = false;

    foreach ($daftar as $surat) {
        foreach (QoriBelajar::cases() as $qori) {
            try {
                $this->line("Surat {$surat} ({$qori->value}): ".$impor($surat, $qori).' kata');
            } catch (Throwable $e) {
                $gagal = true;
                $this->error("Surat {$surat} ({$qori->value}) gagal: {$e->getMessage()}");
            }
        }
    }

    return $gagal ? 1 : 0;
})->purpose('Impor kata Utsmani & waktu per kata dari Quran.com untuk fitur belajar');

Artisan::command('pencarian:sugesti', function (BuatSugestiCari $buat) {
    // Sugesti dan riset hanya memakai 30 hari terakhir; sisanya tidak perlu disimpan (lihat Kebijakan Privasi).
    PencarianHarian::where('tanggal', '<', today()->subDays(90)->toDateString())->delete();
    $sugesti = $buat();
    $this->info($sugesti ? 'Sugesti: '.implode(' · ', $sugesti) : 'Data pencarian belum cukup; beranda memakai daftar bawaan.');

    return 0;
})->purpose('Susun ulang sugesti pencarian dari kata terbanyak dicari 30 hari terakhir');

// Butuh cron `php artisan schedule:run` tiap menit di server.
Schedule::command('pencarian:sugesti')->dailyAt('02:00')->timezone('Asia/Jakarta');
// Memuat alamat IP; masa simpan ini tertulis di Kebijakan Privasi.
Schedule::call(fn () => PutarMurottal::where('created_at', '<', now()->subDays(90))->delete())->dailyAt('02:10')->timezone('Asia/Jakarta');
