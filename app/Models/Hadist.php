<?php

namespace App\Models;

use App\Enums\Derajat;
use App\Enums\Kitab;
use Database\Factories\HadistFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hadist extends Model
{
    /** @use HasFactory<HadistFactory> */
    use HasFactory;

    protected $table = 'hadist';

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kitab' => Kitab::class,
            'derajat' => 'array',
            'derajat_ringkas' => Derajat::class,
            'dikoreksi_at' => 'datetime',
        ];
    }
}
