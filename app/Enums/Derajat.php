<?php

namespace App\Enums;

enum Derajat: string
{
    case Shahih = 'shahih';
    case Hasan = 'hasan';
    case Dhaif = 'dhaif';
    case Maudhu = 'maudhu';

    /**
     * Ringkas penilaian ulama dari dataset jadi satu derajat.
     *
     * @param  array<int, array{name: string, grade: string}>  $grades
     */
    public static function dariPenilaian(Kitab $kitab, array $grades): ?self
    {
        if (in_array($kitab, [Kitab::Bukhari, Kitab::Muslim], true)) {
            return self::Shahih;
        }

        $penilaian = collect($grades)->firstWhere('name', 'Al-Albani') ?? ($grades[0] ?? null);
        $teks = strtolower($penilaian['grade'] ?? '');

        return match (true) {
            str_contains($teks, 'sahih') => self::Shahih,
            str_contains($teks, 'hasan') => self::Hasan,
            preg_match("/da'?if|dhaif|munkar|shadh/", $teks) === 1 => self::Dhaif,
            preg_match('/ma[uw]?d[uo]|batil/', $teks) === 1 => self::Maudhu,
            default => null,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Shahih => 'Shahih',
            self::Hasan => 'Hasan',
            self::Dhaif => "Dha'if",
            self::Maudhu => 'Maudhu',
        };
    }

    /** Kelas Tailwind untuk label derajat. */
    public function warna(): string
    {
        return match ($this) {
            self::Shahih => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
            self::Hasan => 'bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-300',
            self::Dhaif => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
            self::Maudhu => 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-300',
        };
    }
}
