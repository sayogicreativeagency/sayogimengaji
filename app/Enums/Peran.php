<?php

namespace App\Enums;

enum Peran: string
{
    case Superadmin = 'superadmin';
    case Editor = 'editor';
    case Pengguna = 'pengguna';

    public function label(): string
    {
        return match ($this) {
            self::Superadmin => 'Superadmin',
            self::Editor => 'Editor',
            self::Pengguna => 'Pengguna',
        };
    }

    public function bisaMasukAdmin(): bool
    {
        return $this !== self::Pengguna;
    }
}
