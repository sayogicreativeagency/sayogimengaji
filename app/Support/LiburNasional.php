<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/** Libur nasional & cuti bersama Indonesia dari kalender publik Google (mengikuti SKB 3 Menteri). */
final class LiburNasional
{
    public const URL = 'https://calendar.google.com/calendar/ical/id.indonesian%23holiday%40group.v.calendar.google.com/public/basic.ics';

    /** @return array<string, list<string>> 'Y-m-d' → nama libur; kosong bila sumber tak terjangkau. */
    public static function semua(): array
    {
        $libur = Cache::get('libur-nasional');
        if ($libur !== null) {
            return $libur;
        }

        try {
            $libur = self::urai(Http::timeout(10)->get(self::URL)->throw()->body());
            Cache::put('libur-nasional', $libur, now()->addWeek());
        } catch (Throwable) {
            $libur = [];
            Cache::put('libur-nasional', $libur, now()->addHour()); // jangan memukul Google tiap kunjungan saat gagal
        }

        return $libur;
    }

    /** @return array<string, list<string>> */
    public static function urai(string $ics): array
    {
        $libur = [];
        // Baris lanjutan ICS diawali spasi; sambungkan dulu.
        $ics = preg_replace("/\r?\n[ \t]/", '', $ics);

        foreach (explode('BEGIN:VEVENT', $ics) as $acara) {
            // Kalender ini juga memuat "Perayaan" (bukan hari libur); ambil yang berlabel libur nasional saja.
            if (! preg_match('/^DESCRIPTION:Hari libur nasional/m', $acara)
                || ! preg_match('/^DTSTART;VALUE=DATE:(\d{4})(\d{2})(\d{2})/m', $acara, $tanggal)
                || ! preg_match('/^SUMMARY:(.+)$/m', $acara, $nama)) {
                continue;
            }
            $libur["{$tanggal[1]}-{$tanggal[2]}-{$tanggal[3]}"][] = stripcslashes(trim($nama[1]));
        }
        ksort($libur);

        return $libur;
    }
}
