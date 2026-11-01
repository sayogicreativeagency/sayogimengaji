<?php

namespace App\Enums;

/** Qori untuk fitur belajar: audio + waktu per kata dari Quran.com (bukan equran.id seperti murottal). */
enum QoriBelajar: string
{
    case HusaryMuallim = 'husary-muallim';
    case Afasi = 'afasi';

    public function nama(): string
    {
        return match ($this) {
            self::HusaryMuallim => 'Mahmoud Khalil Al-Husary (Muallim, pelan)',
            self::Afasi => 'Misyari Rasyid Al-Afasi',
        };
    }

    /** ID recitation di API Quran.com. */
    public function idQuranCom(): int
    {
        return match ($this) {
            self::HusaryMuallim => 12,
            self::Afasi => 7,
        };
    }

    /** Awalan URL audio per ayat; JS menambahkan "SSSAAA.mp3". */
    public function urlAudio(): string
    {
        return match ($this) {
            self::HusaryMuallim => 'https://mirrors.quranicaudio.com/everyayah/Husary_Muallim_128kbps/',
            self::Afasi => 'https://verses.quran.com/Alafasy/mp3/',
        };
    }
}
