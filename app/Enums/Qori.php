<?php

namespace App\Enums;

/** Qori yang audionya tersedia di CDN equran.id. */
enum Qori: string
{
    case Juhany = 'juhany';
    case Qasim = 'qasim';
    case Sudais = 'sudais';
    case IbrahimDossari = 'ibrahim-dossari';
    case Afasi = 'afasi';
    case YasserDosari = 'yasser-dosari';

    public const BAWAAN = self::Afasi;

    public function nama(): string
    {
        return match ($this) {
            self::Juhany => 'Abdullah Al-Juhany',
            self::Qasim => 'Abdul Muhsin Al-Qasim',
            self::Sudais => 'Abdurrahman As-Sudais',
            self::IbrahimDossari => 'Ibrahim Al-Dossari',
            self::Afasi => 'Misyari Rasyid Al-Afasi',
            self::YasserDosari => 'Yasser Al-Dosari',
        };
    }

    /** Awalan URL audio per ayat; JS menambahkan "SSSAAA.mp3" (nomor surat & ayat 3 digit). */
    public function urlAudio(): string
    {
        $folder = match ($this) {
            self::Juhany => 'Abdullah-Al-Juhany',
            self::Qasim => 'Abdul-Muhsin-Al-Qasim',
            self::Sudais => 'Abdurrahman-as-Sudais',
            self::IbrahimDossari => 'Ibrahim-Al-Dossari',
            self::Afasi => 'Misyari-Rasyid-Al-Afasi',
            self::YasserDosari => 'Yasser-Al-Dosari',
        };

        return "https://cdn.equran.id/audio-partial/{$folder}/";
    }
}
