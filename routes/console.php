<?php

use App\Actions\ImporHadist;
use App\Actions\ImporQuran;
use App\Enums\Kitab;
use Illuminate\Support\Facades\Artisan;

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
