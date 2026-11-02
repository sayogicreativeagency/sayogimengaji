<?php

namespace App\Http\Controllers;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use IntlDateFormatter;

/** Kalender Masehi sebulan dengan tanggal Hijriah (Umm al-Qura) di tiap hari. */
class KalenderController extends Controller
{
    public function tampil(Request $request)
    {
        $hariIni = CarbonImmutable::now('Asia/Jakarta')->startOfDay();
        $bulan = $request->query('bulan');
        $awal = is_string($bulan) && preg_match('/^(19|20)\d\d-(0[1-9]|1[0-2])$/', $bulan)
            ? CarbonImmutable::createFromFormat('!Y-m', $bulan, 'Asia/Jakarta')
            : $hariIni->startOfMonth();

        $hijriah = new IntlDateFormatter('id_ID@calendar=islamic-umalqura', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Asia/Jakarta', IntlDateFormatter::TRADITIONAL, 'd|MMMM|y');
        $hari = [];
        for ($i = 0; $i < $awal->daysInMonth; $i++) {
            $tanggal = $awal->addDays($i);
            [$tanggalH, $bulanH, $tahunH] = explode('|', $hijriah->format($tanggal->setTime(12, 0)));
            $hari[] = ['tanggal' => $tanggal, 'hijriah' => (int) $tanggalH, 'bulan_hijriah' => "{$bulanH} {$tahunH} H"];
        }

        return view('kalender', [
            'awal' => $awal,
            'hari' => $hari,
            'hariIni' => $hariIni,
            'bulanHijriah' => collect($hari)->pluck('bulan_hijriah')->unique()->implode(' – '),
        ]);
    }
}
