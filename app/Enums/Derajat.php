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
}
