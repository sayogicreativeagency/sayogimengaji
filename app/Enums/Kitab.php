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
