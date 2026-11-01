<?php

use App\Actions\ImporHadist;
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
