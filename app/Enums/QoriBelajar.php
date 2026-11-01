<?php

namespace App\Enums;

use App\Models\Pengaturan;

/** Qori untuk fitur belajar: audio + waktu per kata dari Quran.com (bukan equran.id seperti murottal). */
enum QoriBelajar: string
{
    case HusaryMuallim = 'husary-muallim';
    case Afasi = 'afasi';

    /**
     * Qori yang tidak dinonaktifkan di admin (Pengaturan qori), urutan sesuai deklarasi.
     *
     * @return list<self>
     */
    public static function aktif(): array
    {
        $nonaktif = Pengaturan::ambil('qori_belajar_nonaktif', []);

        return array_values(array_filter(self::cases(), fn (self $q) => ! in_array($q->value, $nonaktif, true)));
    }

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
