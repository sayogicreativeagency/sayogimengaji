<?php

namespace App\Enums;

enum Kitab: string
{
    case Bukhari = 'bukhari';
    case Muslim = 'muslim';
    case AbuDawud = 'abudawud';
    case Tirmidhi = 'tirmidhi';
    case Nasai = 'nasai';
    case IbnuMajah = 'ibnmajah';
    case Malik = 'malik';

    public function label(): string
    {
        return match ($this) {
            self::Bukhari => 'Shahih Bukhari',
            self::Muslim => 'Shahih Muslim',
            self::AbuDawud => 'Sunan Abu Dawud',
            self::Tirmidhi => 'Sunan Tirmidzi',
            self::Nasai => "Sunan Nasa'i",
            self::IbnuMajah => 'Sunan Ibnu Majah',
            self::Malik => "Muwaththa' Malik",
        };
    }

    /** Penyusun kitab dan tahun wafatnya (Hijriah). */
    public function penyusun(): string
    {
        return match ($this) {
            self::Bukhari => 'Imam al-Bukhari (w. 256 H)',
            self::Muslim => 'Imam Muslim (w. 261 H)',
            self::AbuDawud => 'Imam Abu Dawud (w. 275 H)',
            self::Tirmidhi => 'Imam at-Tirmidzi (w. 279 H)',
            self::Nasai => "Imam an-Nasa'i (w. 303 H)",
            self::IbnuMajah => 'Imam Ibnu Majah (w. 273 H)',
            self::Malik => 'Imam Malik (w. 179 H)',
        };
    }

    /** Nama perawi untuk rujukan "HR. ...". */
    public function perawi(): string
    {
        return match ($this) {
            self::Bukhari => 'Bukhari',
            self::Muslim => 'Muslim',
            self::AbuDawud => 'Abu Dawud',
            self::Tirmidhi => 'Tirmidzi',
            self::Nasai => "Nasa'i",
            self::IbnuMajah => 'Ibnu Majah',
            self::Malik => 'Malik',
        };
    }
}
