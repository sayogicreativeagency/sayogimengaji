<?php

namespace App\Http\Controllers;

use App\Support\LiburNasional;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use IntlDateFormatter;

/** Kalender Masehi sebulan dengan tanggal Hijriah (Umm al-Qura) di tiap hari, libur nasional, dan hari besar Islam. */
class KalenderController extends Controller
{
    /** [bulan Hijriah, tanggal] → nama. */
    private const HARI_ISLAM = [
        '1-1' => 'Tahun Baru Islam',
        '1-10' => 'Hari Asyura',
        '3-12' => 'Maulid Nabi Muhammad ﷺ',
        '7-27' => "Isra Mi'raj",
        '8-15' => "Nisfu Sya'ban",
        '9-1' => 'Awal Ramadhan',
        '9-17' => "Nuzulul Qur'an",
        '10-1' => 'Idul Fitri',
        '12-9' => 'Hari Arafah',
        '12-10' => 'Idul Adha',
    ];

    public function tampil(Request $request)
    {
        $hariIni = CarbonImmutable::now('Asia/Jakarta')->startOfDay();
        $bulan = $request->query('bulan');
        $awal = is_string($bulan) && preg_match('/^(19|20)\d\d-(0[1-9]|1[0-2])$/', $bulan)
            ? CarbonImmutable::createFromFormat('!Y-m', $bulan, 'Asia/Jakarta')
            : $hariIni->startOfMonth();

        $hijriah = new IntlDateFormatter('id_ID@calendar=islamic-umalqura', IntlDateFormatter::NONE, IntlDateFormatter::NONE, 'Asia/Jakarta', IntlDateFormatter::TRADITIONAL, 'd|M|MMMM|y');
        $libur = LiburNasional::semua();
        $hari = [];
        $acara = [];
        for ($i = 0; $i < $awal->daysInMonth; $i++) {
            $tanggal = $awal->addDays($i);
            [$tanggalH, $nomorBulanH, $bulanH, $tahunH] = explode('|', $hijriah->format($tanggal->setTime(12, 0)));
            $namaLibur = $libur[$tanggal->format('Y-m-d')] ?? [];
            $hari[] = ['tanggal' => $tanggal, 'hijriah' => (int) $tanggalH, 'bulan_hijriah' => "{$bulanH} {$tahunH} H", 'libur' => $namaLibur];

            foreach ($namaLibur as $nama) {
                $acara[] = ['tanggal' => $tanggal, 'nama' => $nama, 'jenis' => str_starts_with($nama, 'Cuti Bersama') ? 'Cuti bersama' : 'Libur nasional'];
            }
            if ($namaIslam = self::HARI_ISLAM[((int) $nomorBulanH).'-'.((int) $tanggalH)] ?? null) {
                $acara[] = ['tanggal' => $tanggal, 'nama' => "{$namaIslam} ({$tanggalH} {$bulanH} {$tahunH} H)", 'jenis' => 'Hari Islam'];
            }
        }

        return view('kalender', [
            'awal' => $awal,
            'hari' => $hari,
            'hariIni' => $hariIni,
            'acara' => $acara,
            'adaDataLibur' => $libur !== [],
            'bulanHijriah' => collect($hari)->pluck('bulan_hijriah')->unique()->implode(' – '),
        ]);
    }
}
